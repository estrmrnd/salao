<?php

require_once '../config/headers.php';
require_once '../models/Horario.php';


$profissional_id = $_GET['profissional_id'] ?? null;
$data = $_GET['data'] ?? null;
$duracao_min = $_GET['duracao_min'] ?? 60;


if (!$profissional_id || !$data) {

    http_response_code(400);

    echo json_encode([
        'erro' => 'Informe profissional_id e data'
    ]);

    exit;
}


if (!strtotime($data)) {

    http_response_code(400);

    echo json_encode([
        'erro' => 'Data inválida'
    ]);

    exit;
}



$model = new Horario();



$dia_semana = date(
    'N',
    strtotime($data)
);



$expediente = $model->buscarExpediente(
    $profissional_id,
    $dia_semana
);



if (!$expediente) {

    echo json_encode([]);

    exit;
}



$bloqueios = $model->buscarBloqueios(
    $profissional_id,
    $data
);



$agendamentos = $model->buscarAgendamentos(
    $profissional_id,
    $data
);



function estaOcupado(
    $hora,
    $duracao,
    $bloqueios,
    $agendamentos
) {

    $inicio = strtotime($hora);

    $fim = $inicio + ($duracao * 60);



    foreach ($bloqueios as $bloqueio) {

        $inicioBloqueio = strtotime(
            $bloqueio['inicio']
        );

        $fimBloqueio = strtotime(
            $bloqueio['fim']
        );


        if (
            $inicio < $fimBloqueio &&
            $fim > $inicioBloqueio
        ) {

            return true;
        }
    }



    foreach ($agendamentos as $agendamento) {

        $inicioAgendamento = strtotime(
            $agendamento['inicio']
        );


        $fimAgendamento =
            $inicioAgendamento +
            ($agendamento['duracao_min'] * 60);



        if (
            $inicio < $fimAgendamento &&
            $fim > $inicioAgendamento
        ) {

            return true;
        }
    }



    return false;
}



$slots = [];

$intervalo = 30;


$inicio = strtotime(
    $data . ' ' . $expediente['inicio']
);


$fim = strtotime(
    $data . ' ' . $expediente['fim']
);



$agora = time();



while (
    $inicio + ($duracao_min * 60)
    <=
    $fim
) {


    $hora = date(
        'H:i',
        $inicio
    );


    if (
        $inicio > $agora &&
        !estaOcupado(
            $hora,
            $duracao_min,
            $bloqueios,
            $agendamentos
        )
    ) {

        $slots[] = $hora;

    }


    $inicio += $intervalo * 60;

}



echo json_encode($slots);