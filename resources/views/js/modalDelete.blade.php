<script language="JavaScript">

    window.onload = function() {

        $('#deleteModal').on('show.bs.modal', function(e) {
            $(this).find('#btn-del').attr('href', $(e.relatedTarget).data('href'));
        });

    };

</script>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Elimina</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Confermi l'eminazione?

                <br /><br />

                <div class="row">
                    <div class="col-lg-6">

                        <a href="#" id="btn-del" class="btn btn-danger btn-block">Sì</a>

                    </div>
                    <div class="col-lg-6">

                        <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">No</button>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
