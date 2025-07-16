@extends('layouts.app')

@section('title', 'Emergency Heat Map - MDRRMO Maramag')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Your existing content here -->
    <!-- Page Header with Emergency Response Styling -->
    <div class="row align-items-center mb-4">
        <div class="col">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-map-marked-alt text-primary fs-5"></i>
                    </div>
                </div>
                <div>
                    <h1 class="h4 mb-1 text-dark fw-bold">Emergency Heat Map</h1>
                    <p class="text-muted mb-0 small">Visual incident analysis for Maramag, Bukidnon</p>
                </div>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="toggleFilters()">
                    <i class="fas fa-filter me-1"></i>Filters
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="refreshMap()">
                    <i class="fas fa-sync-alt me-1"></i>Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-exclamation-triangle text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Total Incidents</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $totalIncidents ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-clock text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">This Month</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $monthlyIncidents ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-chart-area text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">High Density Areas</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $hotspots ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-map-pin text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-muted small">Mapped Locations</div>
                            <div class="h5 mb-0 fw-bold text-dark">{{ $mappedIncidents ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Panel (Initially Hidden) -->
    <div class="row mb-4" id="filterPanel" style="display: none;">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-filter me-2"></i>Filter Controls</h6>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-medium">Emergency Type</label>
                            <select class="form-select form-select-sm" id="incidentTypeFilter">
                                <option value="">All Types</option>
                                <option value="vehicle_vs_vehicle">Vehicle Collision</option>
                                <option value="vehicle_vs_pedestrian">Vehicle vs Pedestrian</option>
                                <option value="vehicle_vs_animals">Vehicle vs Animals</option>
                                <option value="vehicle_vs_property">Vehicle vs Property</option>
                                <option value="vehicle_alone">Single Vehicle</option>
                                <option value="maternity">Medical Emergency</option>
                                <option value="stabbing_shooting">Violence Emergency</option>
                                <option value="transport_to_hospital">Medical Transport</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-medium">Severity Level</label>
                            <select class="form-select form-select-sm" id="severityFilter">
                                <option value="">All Levels</option>
                                <option value="minor">Minor</option>
                                <option value="moderate">Moderate</option>
                                <option value="severe">Severe</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-medium">Date From</label>
                            <input type="date" class="form-control form-control-sm" id="dateFromFilter">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-medium">Date To</label>
                            <input type="date" class="form-control form-control-sm" id="dateToFilter">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-muted small fw-medium">Actions</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm" onclick="applyFilters()">
                                    <i class="fas fa-search me-1"></i>Apply
                                </button>
                                <button class="btn btn-outline-secondary btn-sm" onclick="clearFilters()">
                                    <i class="fas fa-times me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Map Container -->
    <div class="row">
        <div class="col-lg-9 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-map me-2"></i>Emergency Incident Heat Map</h6>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleHeatLayer()">
                                <i class="fas fa-layer-group me-1"></i>Toggle Heat
                            </button>
                            <button class="btn btn-outline-primary btn-sm" onclick="centerMap()">
                                <i class="fas fa-crosshairs me-1"></i>Center
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="heatMap" style="height: 600px; width: 100%;"></div>
                </div>
            </div>
        </div>

        <!-- Map Information Panel -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-info-circle me-2"></i>Map Information</h6>
                </div>
                <div class="card-body">
                    <!-- Heat Map Legend -->
                    <div class="mb-4">
                        <h6 class="text-dark fw-medium mb-3">Incident Density</h6>
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3" style="width: 20px; height: 20px; background: linear-gradient(to right, #0dcaf0, #0d6efd); border-radius: 4px;"></div>
                            <span class="small text-muted">Low - Moderate</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3" style="width: 20px; height: 20px; background: linear-gradient(to right, #fd7e14, #dc3545); border-radius: 4px;"></div>
                            <span class="small text-muted">High - Critical</span>
                        </div>
                    </div>

                    <!-- Severity Markers -->
                    <div class="mb-4">
                        <h6 class="text-dark fw-medium mb-3">Severity Levels</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-circle text-info me-2"></i>
                            <span class="small text-muted">Minor Incidents</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-circle text-primary me-2"></i>
                            <span class="small text-muted">Moderate Incidents</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-circle text-warning me-2"></i>
                            <span class="small text-muted">Severe Incidents</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-circle text-danger me-2"></i>
                            <span class="small text-muted">Critical Incidents</span>
                        </div>
                    </div>

                    <!-- Map Controls Info -->
                    <div class="mb-4">
                        <h6 class="text-dark fw-medium mb-3">Map Controls</h6>
                        <ul class="list-unstyled mb-0">
                            <li class="small text-muted mb-1"><i class="fas fa-mouse-pointer me-2"></i>Click pins for details</li>
                            <li class="small text-muted mb-1"><i class="fas fa-search-plus me-2"></i>Scroll to zoom</li>
                            <li class="small text-muted mb-1"><i class="fas fa-hand-rock me-2"></i>Drag to pan</li>
                            <li class="small text-muted mb-1"><i class="fas fa-layer-group me-2"></i>Toggle heat overlay</li>
                        </ul>
                    </div>

                    <!-- Current View Status -->
                    <div class="bg-light rounded p-3">
                        <h6 class="text-dark fw-medium mb-2">Current View</h6>
                        <div class="small text-muted">
                            <div>Center: <span id="mapCenter">Maramag, Bukidnon</span></div>
                            <div>Zoom Level: <span id="mapZoom">12</span></div>
                            <div>Visible Incidents: <span id="visibleIncidents">{{ $totalIncidents ?? 0 }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Incidents Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-dark fw-medium"><i class="fas fa-list me-2"></i>Recent Incidents on Map</h6>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-external-link-alt me-1"></i>View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($recentIncidents) && $recentIncidents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 text-muted small fw-medium">Incident #</th>
                                        <th class="border-0 text-muted small fw-medium">Type</th>
                                        <th class="border-0 text-muted small fw-medium">Location</th>
                                        <th class="border-0 text-muted small fw-medium">Severity</th>
                                        <th class="border-0 text-muted small fw-medium">Date</th>
                                        <th class="border-0 text-muted small fw-medium">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentIncidents as $incident)
                                    <tr onclick="centerMapOnIncident({{ $incident->latitude }}, {{ $incident->longitude }})" style="cursor: pointer;">
                                        <td class="text-primary fw-medium">{{ $incident->incident_number }}</td>
                                        <td>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                {{ ucwords(str_replace('_', ' ', $incident->incident_type)) }}
                                            </span>
                                        </td>
                                        <td class="text-muted">{{ $incident->location }}</td>
                                        <td>
                                            @switch($incident->severity_level)
                                                @case('minor')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Minor</span>
                                                    @break
                                                @case('moderate')
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">Moderate</span>
                                                    @break
                                                @case('severe')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Severe</span>
                                                    @break
                                                @case('critical')
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">Critical</span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td class="text-muted small">{{ $incident->incident_datetime->format('M j, Y') }}</td>
                                        <td>
                                            @switch($incident->status)
                                                @case('pending')
                                                    <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                                    @break
                                                @case('responding')
                                                    <span class="badge bg-info bg-opacity-10 text-info">Responding</span>
                                                    @break
                                                @case('resolved')
                                                    <span class="badge bg-success bg-opacity-10 text-success">Resolved</span>
                                                    @break
                                                @case('closed')
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Closed</span>
                                                    @break
                                            @endswitch
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-map-marker-alt text-muted fs-1 mb-3"></i>
                            <h6 class="text-muted">No incidents with location data found</h6>
                            <p class="text-muted small">Start by <a href="{{ route('incidents.create') }}" class="text-decoration-none">reporting an incident</a> with location coordinates.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Leaflet CSS and JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
// Heat Map Implementation
let map, heatLayer, markers = [];
let isHeatLayerVisible = true;

// Initialize the map
function initMap() {
    // Center on Maramag, Bukidnon
    map = L.map('heatMap').setView([7.7167, 125.0167], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Z-index fix for map after initialization
    setTimeout(() => {
        const mapContainer = document.getElementById('heatMap');
        if (mapContainer) {
            mapContainer.style.zIndex = '1';
            mapContainer.style.position = 'relative';
        }

        // Force all leaflet panes to low z-index
        const leafletPanes = document.querySelectorAll('.leaflet-pane');
        leafletPanes.forEach(pane => {
            pane.style.zIndex = '1';
        });

        // Ensure navbar stays on top
        const navbar = document.querySelector('.navbar');
        if (navbar) {
            navbar.style.zIndex = '9999';
            navbar.style.position = 'relative';
        }

        // Fix dropdown z-index
        const dropdowns = document.querySelectorAll('.dropdown-content');
        dropdowns.forEach(dropdown => {
            dropdown.style.zIndex = '10000';
        });

        console.log('Z-index fix applied');
    }, 100);

    // Sample incident data - replace with actual data from controller
    const incidentData = @json($incidents ?? []);

    if (incidentData.length > 0) {
        initHeatLayer(incidentData);
        addIncidentMarkers(incidentData);
    }

    // Update map info
    updateMapInfo();

    // Map event listeners
    map.on('zoomend moveend', updateMapInfo);
}

// Rest of your existing JavaScript functions...
function initHeatLayer(incidents) {
    const heatData = incidents.map(incident => {
        if (incident.latitude && incident.longitude) {
            const intensity = getSeverityWeight(incident.severity_level);
            return [incident.latitude, incident.longitude, intensity];
        }
    }).filter(point => point !== undefined);

    if (heatData.length > 0) {
        heatLayer = L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.0: '#0dcaf0',
                0.3: '#0d6efd',
                0.6: '#fd7e14',
                1.0: '#dc3545'
            }
        }).addTo(map);
    }
}

function addIncidentMarkers(incidents) {
    incidents.forEach(incident => {
        if (incident.latitude && incident.longitude) {
            const marker = L.marker([incident.latitude, incident.longitude])
                .bindPopup(createPopupContent(incident));
            marker.options.icon = createSeverityIcon(incident.severity_level);
            marker.addTo(map);
            markers.push(marker);
        }
    });
}

function createSeverityIcon(severity) {
    const colors = {
        'minor': '#0dcaf0',
        'moderate': '#0d6efd',
        'severe': '#fd7e14',
        'critical': '#dc3545'
    };

    return L.divIcon({
        className: 'custom-marker',
        html: `<div style="background-color: ${colors[severity] || '#6c757d'}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });
}

function createPopupContent(incident) {
    const severityBadges = {
        'minor': '<span class="badge bg-info">Minor</span>',
        'moderate': '<span class="badge bg-primary">Moderate</span>',
        'severe': '<span class="badge bg-warning">Severe</span>',
        'critical': '<span class="badge bg-danger">Critical</span>'
    };

    return `
        <div class="p-2">
            <h6 class="mb-2 fw-bold">${incident.incident_number}</h6>
            <p class="mb-1"><strong>Type:</strong> ${incident.incident_type.replace('_', ' ')}</p>
            <p class="mb-1"><strong>Location:</strong> ${incident.location}</p>
            <p class="mb-1"><strong>Severity:</strong> ${severityBadges[incident.severity_level] || incident.severity_level}</p>
            <p class="mb-2"><strong>Date:</strong> ${new Date(incident.incident_datetime).toLocaleDateString()}</p>
            <a href="/incidents/${incident.id}" class="btn btn-primary btn-sm">View Details</a>
        </div>
    `;
}

function getSeverityWeight(severity) {
    const weights = {
        'minor': 0.3,
        'moderate': 0.5,
        'severe': 0.8,
        'critical': 1.0
    };
    return weights[severity] || 0.5;
}

function toggleHeatLayer() {
    if (isHeatLayerVisible && heatLayer) {
        map.removeLayer(heatLayer);
        isHeatLayerVisible = false;
    } else if (heatLayer) {
        map.addLayer(heatLayer);
        isHeatLayerVisible = true;
    }
}

function centerMap() {
    map.setView([7.7167, 125.0167], 12);
}

function centerMapOnIncident(lat, lng) {
    map.setView([lat, lng], 16);
}

function toggleFilters() {
    const panel = document.getElementById('filterPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function refreshMap() {
    location.reload();
}

function updateMapInfo() {
    document.getElementById('mapZoom').textContent = map.getZoom();
    const center = map.getCenter();
    document.getElementById('mapCenter').textContent = `${center.lat.toFixed(4)}, ${center.lng.toFixed(4)}`;
}

function applyFilters() {
    showSuccessToast('Filters applied successfully');
}

function clearFilters() {
    document.getElementById('incidentTypeFilter').value = '';
    document.getElementById('severityFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    showInfoToast('Filters cleared');
}

// Initialize map when page loads
document.addEventListener('DOMContentLoaded', function() {
    initMap();
});
</script>
@endsection

@push('styles')
<style>
/* CRITICAL Z-INDEX FIX FOR NAVBAR AND LEAFLET MAP */

/* Force navbar to be on top of everything */
.navbar {
    z-index: 9999 !important;
    position: relative !important;
}

/* Force map container to be below navbar */
#heatMap {
    z-index: 1 !important;
    position: relative !important;
}

/* Override all Leaflet z-index values */
.leaflet-container,
.leaflet-map-pane,
.leaflet-tile-pane,
.leaflet-overlay-pane,
.leaflet-shadow-pane,
.leaflet-marker-pane,
.leaflet-tooltip-pane,
.leaflet-popup-pane {
    z-index: 1 !important;
}

/* Keep controls visible but below navbar */
.leaflet-control-container,
.leaflet-control-zoom,
.leaflet-control-attribution {
    z-index: 100 !important;
}

/* Popups should be above map but below navbar */
.leaflet-popup {
    z-index: 500 !important;
}

/* Heat layer should stay with map */
.leaflet-heatmap-layer {
    z-index: 1 !important;
}

/* Dropdown menus should be above everything except SweetAlert */
.dropdown-content {
    z-index: 10000 !important;
}

/* Custom marker styling */
.custom-marker {
    background: transparent !important;
    border: none !important;
    z-index: 100 !important;
}

/* Existing styles */
.leaflet-popup-content-wrapper {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.leaflet-popup-content {
    margin: 0;
    font-family: inherit;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.card {
    transition: all 0.2s ease;
}

.btn-sm {
    border-radius: 6px;
}

.badge {
    font-weight: 500;
    border-radius: 6px;
}
</style>
@endpush
