<?php require_once 'acao.php'; ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do Central de Vendas - Extra Máquinas XCMG, São Paulo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="style.css?17">
	<link rel="stylesheet" href="lineicons.css?2">
    <link rel="icon" href="img/favicon.ico">
</head>

<body>
<div class="container">
<div class="center-content">
<img class="lazy-loaded" src="img/logo_extra_maquinas.png" data-lazy-type="image" style="max-width: 200px;" title="Extra Máquinas XCMG">
<h1>Relatório do Central de Vendas</h1>
</div>

    <form method="GET">
        <label for="mes">Data:</label>
            <input type="text" name="mes" id="mes" class="flatpickr">

        <label for="setor">Setor:</label>
            <select name="setor" id="setor">
                <option value="">Todos</option>
                <option value="1">Maquinas</option>
                <option value="2">Peças</option>
            </select>

        <label for="estado">Estado:</label>
            <select name="estado" id="estado">
                <option value="">Todos</option>
                <option value="SP">São Paulo</option>
                <option value="MT">Mato Grosso</option>
                <option value="PA">Pará</option>
                <option value="GO">Goiás</option>
            </select>

        <label for="nome_vendedor">Vendedor:</label>
            <input type="text" name="nome_vendedor" id="nome_vendedor">

        <button type="submit">Filtrar</button>
    </form>

        <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Vendedor</th>
                <th>Estado</th>
                <th>Setor</th>
                <th>Telefone do Cliente</th>
                <th>Produto</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($relatorio = mysqli_fetch_assoc($resultado)) : ?>
                <tr>
                    <td><?php echo $relatorio['data_criacao']; ?></td>
                    <td><?php echo $relatorio['nome_vendedor']; ?></td>
                    <td><?php echo $relatorio['estado']; ?></td>
                    <td><?php echo $relatorio['setor']; ?></td>
                    <td>
                    <?php
                    $celularCliente = $relatorio['celular_cliente'];
                    $celularNumeros = extractNumbers($celularCliente);
                    ?>
                    <a href="https://api.whatsapp.com/send?phone=55<?php echo $celularNumeros; ?>" target="_blank">
                        <?php echo $celularCliente; ?>
                    </a>
                    </td>

                    <td><?php echo $relatorio['produto']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div>
        <p>Total de resultados na busca: <?php echo $totalItens; ?></p>
    </div>

    <?php
    $totalPaginas = ceil($totalItens / $itensPorPagina);

    $urlBase = "?";
    if (isset($_GET['mes']) && $_GET['mes'] != '') {
        $datas = explode(" to ", $_GET['mes']);
        $dataInicial = date('Y-m-d', strtotime(str_replace('/', '-', $datas[0])));
        $dataFinal = date('Y-m-d', strtotime(str_replace('/', '-', $datas[1])));
    
        $sql .= " AND STR_TO_DATE(data_criacao, '%d/%m/%Y') BETWEEN '$dataInicial' AND '$dataFinal'";
    }
    if (isset($_GET['setor'])) {
        $urlBase .= "setor=" . $_GET['setor'] . "&";
    }
    if (isset($_GET['estado'])) {
        $urlBase .= "estado=" . $_GET['estado'] . "&";
    }
    if (isset($_GET['nome_vendedor'])) {
        $urlBase .= "nome_vendedor=" . $_GET['nome_vendedor'] . "&";
    }
    ?>
    
    <?php
    $filtroMes = isset($_GET['mes']) ? $_GET['mes'] : '';
    $filtroSetor = isset($_GET['setor']) ? $_GET['setor'] : '';
    $filtroEstado = isset($_GET['estado']) ? $_GET['estado'] : '';
    $filtroNomeVendedor = isset($_GET['nome_vendedor']) ? $_GET['nome_vendedor'] : '';
    ?>
    <div>
        <?php if ($paginaAtual > 1) : ?>
            <?php
            $paginaAnterior = $paginaAtual - 1;
            $linkAnterior = $urlBase . "pagina=" . $paginaAnterior .
                "&mes=" . urlencode($filtroMes) .
                "&setor=" . urlencode($filtroSetor) .
                "&estado=" . urlencode($filtroEstado) .
                "&nome_vendedor=" . urlencode($filtroNomeVendedor);

            $classePaginaAnterior = ($paginaAnterior == $paginaAtual) ? "active-page" : "";
            ?>
            <a href="<?php echo $linkAnterior; ?>" class="pagination-link <?php echo $classePaginaAnterior; ?>">Anterior</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
            <?php
            $linkPagina = $urlBase . "pagina=" . $i .
                "&mes=" . urlencode($filtroMes) .
                "&setor=" . urlencode($filtroSetor) .
                "&estado=" . urlencode($filtroEstado) .
                "&nome_vendedor=" . urlencode($filtroNomeVendedor);

            $classePaginaAtual = ($i == $paginaAtual) ? "active-page" : "";
            ?>
            <a href="<?php echo $linkPagina; ?>" class="pagination-link <?php echo $classePaginaAtual; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($paginaAtual < $totalPaginas) : ?>
            <?php
            $paginaProxima = $paginaAtual + 1;
            $linkProxima = $urlBase . "pagina=" . $paginaProxima .
                "&mes=" . urlencode($filtroMes) .
                "&setor=" . urlencode($filtroSetor) .
                "&estado=" . urlencode($filtroEstado) .
                "&nome_vendedor=" . urlencode($filtroNomeVendedor);

            $classePaginaProxima = ($paginaProxima == $paginaAtual) ? "active-page" : "";
            ?>
            <a href="<?php echo $linkProxima; ?>" class="pagination-link <?php echo $classePaginaProxima; ?>">Próxima</a>
        <?php endif; ?>
    </div>

    <div class="column-container">
            <div class="column-item">
                <h3>Totais por Estado:</h3>
                <ul>
                    <?php foreach ($estadoTotais as $estado => $total) : ?>
                        <li><?php echo $estado; ?>: <?php echo $total; ?> contatos</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="column-item">
                <h3>Totais por Setor:</h3>
                <ul>
                    <?php foreach ($setorTotais as $setor => $total) : ?>
                        <li><?php echo ($setor == 1) ? 'Máquinas' : 'Peças'; ?>: <?php echo $total; ?> contatos</li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="column-item">
                <h3>Totais por Vendedor:</h3>
                <ul>
                    <?php foreach ($vendedorTotais as $vendedor => $total) : ?>
                        <li><?php echo $vendedor; ?>: <?php echo $total; ?> contatos</li>
                    <?php endforeach; ?>
                </ul>
            </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    flatpickr(".flatpickr", {
        dateFormat: "d/m/Y",
        mode: "range",
        showMonths: 1,
        enableTime: false,
        onChange: function (selectedDates, dateStr, instance) {
            document.getElementById('mes').value = dateStr;
        },
    });
    </script>
    </div>
    </div>

</body>

</html>

