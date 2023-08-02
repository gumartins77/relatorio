<?php

$conexao_dominio = '';
$conexao_usuario = '';
$conexao_senha = '';
$conexao_database = '';

$conn = mysqli_connect($conexao_dominio, $conexao_usuario, $conexao_senha);
mysqli_select_db($conn, $conexao_database);

if (!$conn) {
    die("Erro ao conectar ao banco de dados: " . mysqli_connect_error());
}

$itensPorPagina = 10;

if (isset($_GET['pagina'])) {
    $paginaAtual = $_GET['pagina'];
} else {
    $paginaAtual = 1;
}

$indicePrimeiroItem = ($paginaAtual - 1) * $itensPorPagina;

$sql = "SELECT * FROM relatorios WHERE 1=1";

if (isset($_GET['mes']) && $_GET['mes'] != '') {
    $datas = explode(" to ", $_GET['mes']);
    $dataInicial = date('Y-m-d', strtotime(str_replace('/', '-', $datas[0])));
    
    if (isset($datas[1])) {
        $dataFinal = date('Y-m-d', strtotime(str_replace('/', '-', $datas[1])));
        $sql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') BETWEEN '$dataInicial' AND '$dataFinal'";
    } else {
        $sql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') = '$dataInicial'";
    }
}

if (isset($_GET['setor']) && $_GET['setor'] != '') {
    $setor = $_GET['setor'];
    $sql .= " AND setor = '$setor'";
}

if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $estado = $_GET['estado'];
    $sql .= " AND estado = '$estado'";
}

if (isset($_GET['nome_vendedor']) && $_GET['nome_vendedor'] != '') {
    $nome_vendedor = $_GET['nome_vendedor'];
    $sql .= " AND nome_vendedor = '$nome_vendedor'";
}

function extractNumbers($string) {
    return preg_replace('/[^0-9]/', '', $string);
}

$countSql = str_replace("*", "COUNT(*)", $sql);
$resultado = mysqli_query($conn, $countSql);
$totalItens = mysqli_fetch_assoc($resultado)['COUNT(*)'];

$sql .= " ORDER BY data_criacao DESC";
$sql .= " LIMIT $indicePrimeiroItem, $itensPorPagina";
$resultado = mysqli_query($conn, $sql);

$estadoSql = "SELECT estado, COUNT(*) AS total_por_estado FROM relatorios WHERE 1=1";

if (isset($_GET['mes']) && $_GET['mes'] != '') {
    $datas = explode(" to ", $_GET['mes']);
    $dataInicial = date('Y-m-d', strtotime(str_replace('/', '-', $datas[0])));

    if (isset($datas[1])) {
        $dataFinal = date('Y-m-d', strtotime(str_replace('/', '-', $datas[1])));
        $estadoSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') BETWEEN '$dataInicial' AND '$dataFinal'";
    } else {
        $estadoSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') = '$dataInicial'";
    }
}

if (isset($_GET['setor']) && $_GET['setor'] != '') {
    $setor = $_GET['setor'];
    $estadoSql .= " AND setor = '$setor'";
}

if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $estado = $_GET['estado'];
    $estadoSql .= " AND estado = '$estado'";
}

if (isset($_GET['nome_vendedor']) && $_GET['nome_vendedor'] != '') {
    $nome_vendedor = $_GET['nome_vendedor'];
    $estadoSql .= " AND nome_vendedor = '$nome_vendedor'";
}

$estadoSql .= " GROUP BY estado";
$estadoResult = mysqli_query($conn, $estadoSql);
$estadoTotais = array();
while ($estadoRow = mysqli_fetch_assoc($estadoResult)) {
    $estadoTotais[$estadoRow['estado']] = $estadoRow['total_por_estado'];
}

$setorSql = "SELECT setor, COUNT(*) AS total_por_setor FROM relatorios WHERE 1=1";

if (isset($_GET['mes']) && $_GET['mes'] != '') {
    $datas = explode(" to ", $_GET['mes']);
    $dataInicial = date('Y-m-d', strtotime(str_replace('/', '-', $datas[0])));

    if (isset($datas[1])) {
        $dataFinal = date('Y-m-d', strtotime(str_replace('/', '-', $datas[1])));
        $setorSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') BETWEEN '$dataInicial' AND '$dataFinal'";
    } else {
        $setorSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') = '$dataInicial'";
    }
}

if (isset($_GET['setor']) && $_GET['setor'] != '') {
    $setor = $_GET['setor'];
    $setorSql .= " AND setor = '$setor'";
}

if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $estado = $_GET['estado'];
    $setorSql .= " AND estado = '$estado'";
}

if (isset($_GET['nome_vendedor']) && $_GET['nome_vendedor'] != '') {
    $nome_vendedor = $_GET['nome_vendedor'];
    $setorSql .= " AND nome_vendedor = '$nome_vendedor'";
}

$setorSql .= " GROUP BY setor";
$setorResult = mysqli_query($conn, $setorSql);
$setorTotais = array();
while ($setorRow = mysqli_fetch_assoc($setorResult)) {
    $setorTotais[$setorRow['setor']] = $setorRow['total_por_setor'];
}

$vendedorSql = "SELECT nome_vendedor, COUNT(*) AS total_por_vendedor FROM relatorios WHERE 1=1";

if (isset($_GET['mes']) && $_GET['mes'] != '') {
    $datas = explode(" to ", $_GET['mes']);
    $dataInicial = date('Y-m-d', strtotime(str_replace('/', '-', $datas[0])));

    if (isset($datas[1])) {
        $dataFinal = date('Y-m-d', strtotime(str_replace('/', '-', $datas[1])));
        $vendedorSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') BETWEEN '$dataInicial' AND '$dataFinal'";
    } else {
        $vendedorSql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') = '$dataInicial'";
    }
}

if (isset($_GET['setor']) && $_GET['setor'] != '') {
    $setor = $_GET['setor'];
    $vendedorSql .= " AND setor = '$setor'";
}

if (isset($_GET['estado']) && $_GET['estado'] != '') {
    $estado = $_GET['estado'];
    $vendedorSql .= " AND estado = '$estado'";
}

if (isset($_GET['nome_vendedor']) && $_GET['nome_vendedor'] != '') {
    $nome_vendedor = $_GET['nome_vendedor'];
    $vendedorSql .= " AND nome_vendedor = '$nome_vendedor'";
}

$vendedorSql .= " GROUP BY nome_vendedor";
$vendedorResult = mysqli_query($conn, $vendedorSql);
$vendedorTotais = array();
while ($vendedorRow = mysqli_fetch_assoc($vendedorResult)) {
    $vendedorTotais[$vendedorRow['nome_vendedor']] = $vendedorRow['total_por_vendedor'];
}

mysqli_close($conn);
?>