<?php

namespace App\Controller\Admin;

use Cake\Http\Exception\NotFoundException;

/**
 * @property \App\Model\Table\AnnouncementsTable $Announcements
 */
class AnnouncementsController extends AppAdminController
{
    public function index()
    {
        emptyLogs();
        $query = $this->Announcements->find();
        $announcements = $this->paginate($query);
        foreach ($announcements as $index => $announcement) {
            $announcement->title=json_decode($announcement->title);
            $announcement->content=json_decode($announcement->content);
            
    }


        $this->set('announcements', $announcements);
    }

    public function add()
    {
        $announcement = $this->Announcements->newEntity();

        if ($this->getRequest()->is('post')) {
            $anondata=$this->getRequest()->getData();
            $anondata["title"] = json_encode($anondata["title"]);
            $anondata["content"] = json_encode($anondata["content"]);

            $announcement = $this->Announcements->patchEntity($announcement, $anondata);

            if ($this->Announcements->save($announcement)) {
                $this->Flash->success(__('Announcement has been added.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Oops! There are mistakes in the form. Please make the correction.'));
        }
        $this->set('announcement', $announcement);
    }

    public function edit($id = null)
    {
        if (!$id) {
            throw new NotFoundException(__('Invalid Announcement'));
        }

        if ($this->getRequest()->getQuery('lang') && isset(get_site_languages()[$this->getRequest()->getQuery('lang')])) {
            //$announcement->_locale = $this->getRequest()->getQuery('lang');
            $this->Announcements->locale($this->getRequest()->getQuery('lang'));
        }

        $announcement = $this->Announcements->get($id);
        $announcement->title=json_decode($announcement->title);
        $announcement->content=json_decode($announcement->content);
        if (!$announcement) {
            throw new NotFoundException(__('Invalid Announcement'));
        }

        if ($this->getRequest()->is(['post', 'put'])) {
            $anondata=$this->getRequest()->getData();
            $anondata["title"] = json_encode($anondata["title"]);
            $anondata["content"] = json_encode($anondata["content"]);
            $announcement = $this->Announcements->patchEntity($announcement, $anondata);

            if ($this->Announcements->save($announcement)) {
                $this->Flash->success(__('Announcement has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Oops! There are mistakes in the form. Please make the correction.'));
        }
        $this->set('announcement', $announcement);
    }

    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete']);

        $announcement = $this->Announcements->findById($id)->first();

        if ($this->Announcements->delete($announcement)) {
            $this->Flash->success(__('The announcement with id: {0} has been deleted.', $announcement->id));

            return $this->redirect(['action' => 'index']);
        }
    }
}
