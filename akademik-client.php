<html>
<head><title>INPUT NILAI</title></head>
<body>
<h2>ENTRY DATA MAHASISWA</h2>
<div id="bagInput">
<form method="post">
<table>
<tr><td>TAHUN AKADEMIK</td><td><input type="text" name="thakd"/></td></tr>
<tr><td>SEMESTER</td><td><input type="text" name="smst"/></td></tr>
<tr><td>NIM</td><td><input type="text" name="nim"/></td></tr>
<tr><td>KODE MATA KULIAH</td><td><input type="text" name="kdmk"/></td><td></td></tr>
<tr><td></td><td>
<input name="simpan" type="submit" value="SIMPAN"/>
</td><td></td>
</table>
</form>
</div>
<p>
<?php

if(isset($_GET['simpan'])){
    require_once('lib/nusoap.php');
    $client = new nusoap_client('http://localhost/akademik-service.php?wsdl', false);
    $param_input = array('thakd'=>$_GET['thakd'],'smst'=>$_GET['smst'],'nomhs'=>$_GET['nomhs'],'kdmk'=>$_GET['kdmk']);
    $hasil = $client->call('transaksiKrs', $param_input);
    echo $hasil;
}

?>
</body>
</html>