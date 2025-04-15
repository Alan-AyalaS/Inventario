<?php
function getClientsPerState() {
    $db = Database::getCon();
    $query = "SELECT state, COUNT(*) as total FROM person WHERE kind=1 GROUP BY state";
    $result = $db->query($query);
    
    $clientsPerState = array();
    $totalClients = 0;
    
    while ($row = $result->fetch_assoc()) {
        $clientsPerState[$row['state']] = $row['total'];
        $totalClients += $row['total'];
    }
    
    // Calcular porcentajes
    foreach ($clientsPerState as $state => $count) {
        $clientsPerState[$state] = array(
            'count' => $count,
            'percentage' => ($count / $totalClients) * 100
        );
    }
    
    return $clientsPerState;
}

$clientsPerState = getClientsPerState();

// Mapeo de nombres de estados a códigos (sin acentos)
$stateCodes = array(
    'Aguascalientes' => 'mx-ag',
    'Baja California' => 'mx-bc',
    'Baja California Sur' => 'mx-bs',
    'Campeche' => 'mx-cm',
    'Chiapas' => 'mx-cs',
    'Chihuahua' => 'mx-ch',
    'Ciudad de Mexico' => 'mx-df',
    'Coahuila' => 'mx-co',
    'Colima' => 'mx-cl',
    'Durango' => 'mx-dg',
    'Guanajuato' => 'mx-gj',
    'Guerrero' => 'mx-gr',
    'Hidalgo' => 'mx-hg',
    'Jalisco' => 'mx-ja',
    'Estado de Mexico' => 'mx-mx',
    'Michoacan' => 'mx-mi',
    'Morelos' => 'mx-mo',
    'Nayarit' => 'mx-na',
    'Nuevo Leon' => 'mx-nl',
    'Oaxaca' => 'mx-oa',
    'Puebla' => 'mx-pu',
    'Queretaro' => 'mx-qt',
    'Quintana Roo' => 'mx-qr',
    'San Luis Potosi' => 'mx-sl',
    'Sinaloa' => 'mx-si',
    'Sonora' => 'mx-so',
    'Tabasco' => 'mx-tb',
    'Tamaulipas' => 'mx-tm',
    'Tlaxcala' => 'mx-tl',
    'Veracruz' => 'mx-ve',
    'Yucatan' => 'mx-yu',
    'Zacatecas' => 'mx-za'
);

// Preparar datos para Highmaps
$mapData = array();
foreach ($clientsPerState as $state => $data) {
    if (isset($stateCodes[$state])) {
        $mapData[] = array(
            'code' => $stateCodes[$state],
            'value' => $data['count'],
            'percentage' => $data['percentage']
        );
    }
}

// Ordenar datos por porcentaje para establecer la escala de colores
usort($mapData, function($a, $b) {
    return $b['percentage'] - $a['percentage'];
});

// Encontrar el máximo porcentaje para la escala
$maxPercentage = $mapData[0]['percentage'];

?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Distribución de Clientes por Estado</h3>
    </div>
    <div class="card-body">
        <div id="mapContainer" style="height: 500px; min-width: 310px; max-width: 800px; margin: 0 auto;"></div>
    </div>
</div>

<!-- Highmaps -->
<script src="https://code.highcharts.com/maps/highmaps.js"></script>
<script src="https://code.highcharts.com/maps/modules/exporting.js"></script>

<script>
(async () => {
    const topology = await fetch(
        'https://code.highcharts.com/mapdata/countries/mx/mx-all.topo.json'
    ).then(response => response.json());

    // Datos del mapa
    const mapData = <?php echo json_encode($mapData); ?>;
    
    // Convertir datos al formato requerido por Highmaps
    const data = mapData.map(item => ({
        'hc-key': item.code,
        value: item.value,
        percentage: item.percentage
    }));

    // Create the chart
    Highcharts.mapChart('mapContainer', {
        chart: {
            map: topology,
            backgroundColor: '#f8f9fa'
        },

        title: {
            text: 'Distribución de Clientes por Estado'
        },

        subtitle: {
            text: 'Número de clientes registrados por estado'
        },

        mapNavigation: {
            enabled: true,
            buttonOptions: {
                verticalAlign: 'bottom'
            }
        },

        colorAxis: {
            min: 0,
            max: <?php echo $maxPercentage; ?>,
            minColor: '#E6F3FF',  // Azul muy claro
            maxColor: '#0056B3',  // Azul oscuro
            stops: [
                [0, '#E6F3FF'],
                [0.3, '#66B3FF'],
                [0.6, '#3385FF'],
                [1, '#0056B3']
            ]
        },

        series: [{
            data: data,
            name: 'Clientes',
            states: {
                hover: {
                    color: '#BADA55'
                }
            },
            dataLabels: {
                enabled: true,
                format: '{point.name}'
            },
            tooltip: {
                pointFormat: '{point.name}: {point.value} clientes ({point.percentage:.1f}%)'
            }
        }]
    });
})();
</script>