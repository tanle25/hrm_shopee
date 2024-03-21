<link href="https://unpkg.com/tabulator-tables@5.6.1/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.6.1/dist/js/tabulator.min.js"></script>
<div id="example-table"></div>
<script>
    var table = new Tabulator("#example-table", {
    height:"311px",
    layout:"fitColumns",
    ajaxURL:"{{url('get-data')}}",
    progressiveLoad:"scroll",
    paginationSize:20,
    placeholder:"No Data Set",
    columns:[
        {title:"Name", field:"name", sorter:"string", width:200},
        {title:"Progress", field:"progress", sorter:"number", formatter:"progress"},
        {title:"Gender", field:"gender", sorter:"string"},
        {title:"Rating", field:"rating", formatter:"star", hozAlign:"center", width:100},
        {title:"Favourite Color", field:"col", sorter:"string"},
        {title:"Date Of Birth", field:"dob", sorter:"date", hozAlign:"center"},
        {title:"Driver", field:"car", hozAlign:"center", formatter:"tickCross", sorter:"boolean"},
    ],
});
</script>
