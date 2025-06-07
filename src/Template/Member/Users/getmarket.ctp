
<div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Ingresa tus datos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?php echo $this->Url->build(['action' => 'getmarket']); ?>">
                    <input type="hidden" name="ofert_type" value="Cuentas de Fcebook">
                    <div class="form-group">
                        <label for="whatsapp">Número de WhatsApp:</label>
                        <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Ingrese su número de WhatsApp">
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad de artículos:</label>
                        <input type="number" class="form-control" id="cantidad" name="cantidad" placeholder="Ingrese la cantidad">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                            <?php echo $buttonText; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

