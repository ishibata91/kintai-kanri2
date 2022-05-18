$(document).ready(function(){
    $('#table_id').DataTable();
} );
//未使用
function confirmFunction1(){
    ret = confirm("本当に削除しますか？");
    if (ret==true){
        location.href="delete.php";
    }
}