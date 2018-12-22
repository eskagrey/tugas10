<?php
require_once("lib/nusoap.php");
$server = new nusoap_server();
$server->configureWSDL('akademik-service','http://lab.sinus.ac.id/app');
$server->register('inputMhs', array('nomhs'=>'xsd:string', 'nmmhs'=>'xsd:string', 'psmhs'=>'xsd:string'), array('msg'=>'xsd:string'),
'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#inputMhs', 'rpc','encoded','fungsi ini untuk menginputkan data mahasiswa');
$server->register('updateMhs', array('nomhs'=>'xsd:string', 'nmmhs'=>'xsd:string', 'psmhs'=>'xsd:string'), array('msg'=>'xsd:string'),
'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#updateMhs', 'rpc','encoded','fungsi ini untuk mengupdate data mahasiswa');
$server->register('hapusMhs', array('nomhs'=>'xsd:string'), array('msg'=>'xsd:string'),
'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#hapusMhs', 'rpc','encoded','fungsi ini untuk menghapus data mahasiswa');
$server->register('cariMhs', array('nomhs'=>'xsd:string'), array('msg'=>'xsd:string'),
'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#cariMhs', 'rpc','encoded','fungsi ini untuk mencari data mahasiswa');
$server->register('viewMhsProdi', array('prodi'=>'xsd:string'), array('armhs'=>'tns:ArrayMahasiswa'),'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#viewMhsProdi', 'rpc','encoded','Fungsi ini untuk menampilkan semua data mahasiswa berdasarkan prodi');
$server->register('transaksiKrs', 
    array('thakd'=>'xsd:string', 'semester'=>'xsd:string', 'nomhs'=>'xsd:string', 'arrayMakul'=>'tns:ArrayMatakuliah'), 
    array('$msg'=>'xsd:string'), 'http://lab.sinus.ac.id/app','http://lab.sinus.ac.id/app#viewMhsProdi', 'rpc', 'encoded', 'fungsi ini untuk menampilkan data mahasiswa berdasarkan prodi');

$server->wsdl->addComplexType(
    'ArrayMahasiswa',
    'complexType',
    'array',
    '', 
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:ArrayType','wsdl:arrayType'=>'tns:Mahasiswa[]')
    ),
    'tns:Mahasiswa'
);


//modul 10 ----------------------------------------------------------
$server->wsdl->addComplexType(
    'Matakuliah',
    'complexType',
    'struct',
    'sequence',
    '',
    array(
        'kdmk'=>array('name'=>'kdmk','type'=>'xsd:string'),
        'nmmk'=>array('name'=>'nmmk','type'=>'xsd:string'),
        'sks'=>array('name'=>'sks','type'=>'xsd:string')
    )
);

$server->wsdl->addComplexType(
    'ArrayMatakuliah',
    'complexType',
    'array',
    '',
    'SOAP-ENC:Array',
    array(),
    array(
        array('ref'=>'SOAP-ENC:arrayType','wsdl:arrayType'=>'tns:Matakuliah[]')
    ),
    'tns:Matakuliah'
);

function transaksiKrs($thakd, $smst, $nomhs, $arrMakul){
    $msg = '';
    if (!empty($thakd) && !empty($smst) && !empty($nomhs) && !empty($arrMakul)){
        $cn = mysqli_connect('localhost','root','','akademik');
        mysqli_autocommit($cn, false);
        $valid = true;
        for($i=0; $i<count($arrMakul); $i++){
            $kdmk = $arrMakul[$i]['kdmk'];
            $sql = "insert into nilai(thakd, smst, nim, kdmk) ";
            $sql .= "values ('$thakd','$smst','$nomhs','$kdmk')";
            $hasil = mysqli_query($cn, $sql);
            $valid = $valid && $hasil;
        } 
        if ($valid){
            mysqli_commit($cn);
            $msg = "transaksi KRS berhasil";
        } else{
            mysqli_rollback($cn);
            $msg = "transaksi KRS gagal";
        }
    } else {
        $msg = 'data transaksi tidak valid';
    }
    return $msg;
}


//modul 10 ----------------------------------------------------------

function inputMhs($nomhs, $nmmhs, $psmhs){
    $msg="";
    if(!empty($nomhs) && !empty($nmmhs) && !empty($psmhs)){
        $cn = mysqli_connect('localhost','root','','akademik');
        $sql = "insert into mahasiswa (nim, nama, prodi)values('$nomhs','$nmmhs','$psmhs')";
        $hasil = mysqli_query($cn, $sql);
        if($hasil>0){
            $msg="data berhasil disimpan";
        } else{
            $msg="ups, gagal menyimpan";
        }
    } else{
        $msg="data tidak valid";
    }
    return $msg;
}

function updateMhs($nomhs, $nmmhs, $psmhs){
    $msg="";
    if(!empty($nomhs) && !empty($nmmhs) && !empty($psmhs)){
        $cn = mysqli_connect('localhost','root','','akademik');
        $sql = "update mahasiswa set nama='$nmmhs',prodi='$psmhs' where nim='$nomhs'";
        $hasil = mysqli_query($cn, $sql);
        if($hasil>0){
            $msg="data berhasil diperbarui";
        } else{
            $msg="ups, gagal memperbarui";
        }
    } else{
        $msg="data tidak valid";
    }
    return $msg;
}

function hapusMhs($nomhs){
    $msg="";
    if(!empty($nomhs)){
        $cn = mysqli_connect('localhost','root','','akademik');
        $sql = "delete from mahasiswa where nim='$nomhs'";
        $hasil = mysqli_query($cn, $sql);
        if($hasil>0){
            $msg="data berhasil dihapus";
        } else{
            $msg="ups, gagal menghapus";
        }
    } else{
        $msg="data tidak valid";
    }
    return $msg;
}

function cariMhs($nomhs){
    $msg="";
    if(!empty($nomhs)){
        $cn = mysqli_connect('localhost','root','','akademik');
        $cn = "select nim, nama, prodi from mahasiswa where nim='$nomhs'";
        $hasil = mysqli_query($cn, $sql);
        $data = mysqli_fetch_array($hasil);
        $mhs = array(
            'nim'=>$data['nim'],
            'nama'=>$data['nama'],
            'prodi'=>$data['prodi']
        );
    } else{
        $msg="data tidak ditemukan";
    } return $mhs;
}

function viewMhsProdi($prodi){
    $armhs = array();
    if(!empty($prodi)){
        $cn = mysqli_connect('localhost','root','','akademik');
        $sql = "select nim, nama, prodi from mahasiswa where prodi='$prodi'";
        $hasil = mysqli_query($cn, $sql);
        while($data = mysqli_fetch_array($hasil)){
            $armhs[] = array(
                'nim'=>$data['nim'],
                'nama'=>$data['nama'],
                'prodi'=>$data['prodi']
            );
        }
    } return $armhs;
}


//listener
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:'';
$server->service($HTTP_RAW_POST_DATA);
?>
