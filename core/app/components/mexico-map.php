<?php
include_once "core/controller/Database.php";
include_once "core/app/components/mexico-data.php";

function getClientsPerState() {
    // Obtener todos los clientes directamente de la base de datos
    $sql = "SELECT state, COUNT(*) as count FROM person GROUP BY state";
    $con = Database::getCon();
    $query = $con->query($sql);
    $clientsPerState = array();
    
    // Mapeo de nombres de estados a códigos
    $stateMapping = array(
        'Aguascalientes' => 'AGS',
        'Baja California' => 'BC',
        'Baja California Sur' => 'BCS',
        'Chihuahua' => 'CHIH',
        'Coahuila' => 'COAH',
        'Colima' => 'COL',
        'Durango' => 'DGO',
        'Estado de Mexico' => 'MEX',
        'Guanajuato' => 'GTO',
        'Guerrero' => 'GRO',
        'Jalisco' => 'JAL',
        'Michoacan' => 'MICH',
        'Nuevo Leon' => 'NL',
        'Queretaro' => 'QRO',
        'San Luis Potosi' => 'SLP',
        'Sinaloa' => 'SIN',
        'Tamaulipas' => 'TAMPS',
        'Zacatecas' => 'ZAC'
    );

    while($row = $query->fetch_object()) {
        $state = trim($row->state); // Eliminar espacios en blanco
        $stateCode = isset($stateMapping[$state]) ? $stateMapping[$state] : $state;
        $clientsPerState[$stateCode] = intval($row->count);
    }

    return $clientsPerState;
}

// Datos de clientes
$clientsData = getClientsPerState();
?>

<div class="card mb-4">
    <div class="card-header">
        Distribución de Clientes por Estado
    </div>
    <div class="card-body">
        <div id="mexico-map" style="width: 100%; height: 500px;"></div>
    </div>
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
$(document).ready(function(){
    try {
        // Datos de clientes por estado
        const clientsPerState = <?php echo json_encode($clientsData); ?>;
        console.log('Datos de clientes:', clientsPerState);
        
        // Datos GeoJSON de México
        const mexicoData = <?php echo $mexicoGeoJSON; ?>;
        
        // Inicializar el mapa
        const map = L.map('mexico-map').setView([23.6345, -102.5528], 5);
        
        // Agregar capa de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Función para obtener el color basado en la cantidad de clientes
        function getColor(d) {
            return d > 100 ? '#800026' :
                   d > 50  ? '#BD0026' :
                   d > 20  ? '#E31A1C' :
                   d > 10  ? '#FC4E2A' :
                   d > 5   ? '#FD8D3C' :
                   d > 0   ? '#FEB24C' :
                            '#FFEDA0';
        }

        // Función para el estilo de cada estado
        function style(feature) {
            const stateCode = feature.properties.code;
            const clients = clientsPerState[stateCode] || 0;
            console.log('Estado:', feature.properties.name, 'Código:', stateCode, 'Clientes:', clients);
            return {
                fillColor: getColor(clients),
                weight: 2,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

        // Función para resaltar el estado al pasar el mouse
        function highlightFeature(e) {
            const layer = e.target;
            layer.setStyle({
                weight: 5,
                color: '#666',
                dashArray: '',
                fillOpacity: 0.7
            });
            layer.bringToFront();
            info.update(layer.feature.properties);
        }

        // Función para resetear el estilo al quitar el mouse
        function resetHighlight(e) {
            geojson.resetStyle(e.target);
            info.update();
        }

        // Función para hacer zoom al estado al hacer clic
        function zoomToFeature(e) {
            map.fitBounds(e.target.getBounds());
        }

        // Función para manejar los eventos de cada estado
        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });
        }

        // Crear el control de información
        const info = L.control();
        info.onAdd = function(map) {
            this._div = L.DomUtil.create('div', 'info');
            this.update();
            return this._div;
        };
        info.update = function(props) {
            if (props) {
                const stateCode = props.code;
                const clients = clientsPerState[stateCode] || 0;
                this._div.innerHTML = '<h4>Distribución de Clientes</h4>' +
                    '<b>' + props.name + '</b><br />' +
                    clients + ' cliente' + (clients !== 1 ? 's' : '');
            } else {
                this._div.innerHTML = '<h4>Distribución de Clientes</h4>Pasa el mouse sobre un estado';
            }
        };
        info.addTo(map);

        // Agregar la leyenda
        const legend = L.control({position: 'bottomright'});
        legend.onAdd = function(map) {
            const div = L.DomUtil.create('div', 'info legend');
            const grades = [0, 5, 10, 20, 50, 100];
            const labels = [];
            let from, to;
            for (let i = 0; i < grades.length; i++) {
                from = grades[i];
                to = grades[i + 1];
                labels.push(
                    '<i style="background:' + getColor(from + 1) + '"></i> ' +
                    from + (to ? '&ndash;' + to : '+'));
            }
            div.innerHTML = labels.join('<br>');
            return div;
        };
        legend.addTo(map);

        // Agregar los datos GeoJSON al mapa
        geojson = L.geoJSON(mexicoData, {
            style: style,
            onEachFeature: onEachFeature
        }).addTo(map);

    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
    }
});
</script>

<style>
.info {
    padding: 6px 8px;
    font: 14px/16px Arial, Helvetica, sans-serif;
    background: white;
    background: rgba(255,255,255,0.8);
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    border-radius: 5px;
}
.info h4 {
    margin: 0 0 5px;
    color: #777;
}
.legend {
    line-height: 18px;
    color: #555;
}
.legend i {
    width: 18px;
    height: 18px;
    float: left;
    margin-right: 8px;
    opacity: 0.7;
}
</style> 