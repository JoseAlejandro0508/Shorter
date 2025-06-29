<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<?php
$this->assign('title', __('Create an Account'));
$this->assign('description', __('Register a new membership'));
?>

<strong class="login-box-msg" style="font-size: 50px;padding:10px;background: linear-gradient(90deg, #ff00d6, #0777f7);-webkit-background-clip: text;color: #00000000;font-weight: 700;">
      <?= __('Register a new membership') ?></strong>

<?= $this->Form->create($user, ['id' => 'signup-form']); ?>

<?=
$this->Form->control('username', [
    'label' => false,
    'placeholder' => __('Usuario'),
    'class' => 'form-control'
])
?>

<?=
$this->Form->control('email', [
    'label' => false,
    'placeholder' => __('Email'),
    'class' => 'form-control'
])
?>

<?=
$this->Form->control('password', [
    'label' => false,
    'placeholder' => __('Clave'),
    'class' => 'form-control'
])
?>

<?=
$this->Form->control('password_compare', [
    'type' => 'password',
    'label' => false,
    'placeholder' => __('Introduce la clave nuevamente'),
    'class' => 'form-control'
])
?>

<?php if ((get_option('enable_captcha_signup') == 'yes') && isset_captcha()) : ?>
    <div class="form-group captcha">
        <div id="captchaSignup" style="display: inline-block;"></div>
    </div>
<?php endif; ?>

<div class="form-group">
    <?= $this->Form->control('accept', [
        'type' => 'checkbox',
        'label' => "<b>" . __(
                "Estoy de acuerdo con {0} y {1}.",
                "<a href='" . $this->Url->build('/') . 'pages/terms' . "' target='_blank'>" .
                __('Terminos de uso') . "</a>",
                "<a href='" . $this->Url->build('/') . 'pages/privacy' . "' target='_blank'>" .
                __('Privacy Policy') . "</a>"
            ) . "</b>",
        'escape' => false
    ]) ?>
</div>

<?= $this->Form->button(__('Registrar'), [
    'class' => 'btn btn-primary btn-block btn-flat btn-captcha',
    'id' => 'invisibleCaptchaSignup'
]); ?>

<?= $this->Form->end() ?>

<div class="social-auth-links text-center">
    <p>- <?= __("O") ?> -</p>

    <?php if ((bool)get_option('social_login_facebook', false)) : ?>
        <?php
        echo $this->Form->postLink(
            '<i class="fa fa-facebook"></i> ' . __("Sign in with Facebook"),
            [
                'prefix' => false,
                'plugin' => 'ADmad/SocialAuth',
                'controller' => 'Auth',
                'action' => 'login',
                'provider' => 'facebook',
                '?' => ['redirect' => $this->request->getQuery('redirect')]
            ],
            [
                'class' => 'btn btn-block btn-social btn-facebook',
                'style' => 'margin-bottom: 5px;',
                'escape' => false
            ]
        );
        ?>
    <?php endif; ?>

    <?php if ((bool)get_option('social_login_twitter', false)) : ?>
        <?php
        echo $this->Form->postLink(
            '<i class="fa fa-twitter"></i> ' . __("Sign in with Twitter"),
            [
                'prefix' => false,
                'plugin' => 'ADmad/SocialAuth',
                'controller' => 'Auth',
                'action' => 'login',
                'provider' => 'twitter',
                '?' => ['redirect' => $this->request->getQuery('redirect')]
            ],
            [
                'class' => 'btn btn-block btn-social btn-twitter',
                'style' => 'margin-bottom: 5px;',
                'escape' => false
            ]
        );
        ?>
    <?php endif; ?>

    <?php if ((bool)get_option('social_login_google', false)) : ?>
        <?php
        echo $this->Form->postLink(
            '<i class="fa fa-google"></i> ' . __("Sign in with Google"),
            [
                'prefix' => false,
                'plugin' => 'ADmad/SocialAuth',
                'controller' => 'Auth',
                'action' => 'login',
                'provider' => 'google',
                '?' => ['redirect' => $this->request->getQuery('redirect')]
            ],
            [
                'class' => 'btn btn-block btn-social btn-google',
                'style' => 'margin-bottom: 5px;',
                'escape' => false
            ]
        );
        ?>
    <?php endif; ?>
</div>

<a href="<?= $this->Url->build(['controller' => 'Users', 'action' => 'signin', 'prefix' => 'auth']); ?>"
   class="text-center"><?= __('I already have a membership') ?></a>
