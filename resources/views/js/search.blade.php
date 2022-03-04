<script language="JavaScript">

    function dataSet(ObjDataContainer, jsonData)
    {
        $.each(jsonData, function(k, v) {

            ObjDataContainer.find('[data-id="' + k + '"]').html(v);

        });
    }

    function dataSearch(ObjData, resSearch, codSearch, urlQuery, callforward, callback)
    {
        if (callforward != null)
            callforward();

        $.getJSON(resSearch + urlQuery + '=' + codSearch, function (d) {
            
            if (d != null) {
                dataSet(ObjData, d);
            }

            if (callback != null) {
                callback(d);
            }

        });
    }

</script>
