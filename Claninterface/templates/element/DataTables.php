<?php
/**
 * @var int $orderCol
 * @var string $order asc | desc
 */
?>
<script>
    $(document).ready(function() {
        $(document).ready(function () {
            $('.DataTable').DataTable({
                autoFill : true,
                "lengthMenu" : [ [25, 50, 100, -1 ],
                    [25, 50, 100, "Alle" ] ],
                language : {
                    "sEmptyTable" : "Keine Daten in der Tabelle vorhanden",
                    "sInfo" : "_START_ bis _END_ von _TOTAL_ Einträgen",
                    "sInfoEmpty" : "0 bis 0 von 0 Einträgen",
                    "sInfoFiltered" : "(gefiltert von _MAX_ Einträgen)",
                    "sInfoPostFix" : "",
                    "sInfoThousands" : ".",
                    "sLengthMenu" : "_MENU_ Einträge anzeigen",
                    "sLoadingRecords" : "Wird geladen...",
                    "sProcessing" : "Bitte warten...",
                    "sSearch" : "Suchen",
                    "sZeroRecords" : "Keine Einträge vorhanden.",
                    "oPaginate" : {
                        "sFirst" : "Erste",
                        "sPrevious" : "Zurück",
                        "sNext" : "Nächste",
                        "sLast" : "Letzte"
                    },
                    "oAria" : {
                        "sSortAscending" : ": aktivieren, um Spalte aufsteigend zu sortieren",
                        "sSortDescending" : ": aktivieren, um Spalte absteigend zu sortieren"
                    }
                },
                "order": [[ <?= $orderCol ?>, "<?= $order ?>" ]]
            });
        });
    } );
</script>
