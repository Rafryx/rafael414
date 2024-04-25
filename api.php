<?php
header('Content-Type: application/json');

$subscription_id = $_GET['subscription_id'];
$url = 'http://172.28.96.176/v1/cloudserver_orchestration/instance_list_get/' . $subscription_id . '/';
$api_data = @file_get_contents($url);

if ($api_data === false) {
    // Tratamento de erro: falha na leitura da API
    $error_message = error_get_last();
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Erro ao consultar a API.', 'details' => $error_message]);
} else {
    $api_result = json_decode($api_data);

    if ($api_result === null || !property_exists($api_result, 'result')) {
        // Tratamento de erro: decodificação JSON mal-sucedida ou propriedade 'result' não definida
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Erro ao decodificar JSON da API ou a propriedade \'result\' não está definida.']);
    } else {
        // Retornar cada instância como um objeto no array result
        $instances = explode(' - ', $api_result->result[0]);
        $formatted_instances = [];

        foreach ($instances as $instance) {
            list($key, $value) = explode(': ', $instance);
            $formatted_instances[trim($key)] = trim($value);
        }

        // Responder com os resultados formatados
        echo json_encode(['status' => $api_result->status, 'result' => [$formatted_instances]]);
    }
}
?>
