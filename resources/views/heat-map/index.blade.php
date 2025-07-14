@extends('layouts.app')

@section('title', 'Accident Heat Map - MDRRMO Maramag')

@section('page-header')
    <div class="row align-items-center">
        <div class="col">
            <h1 class="page-title">Accident Heat Map</h1>
            <p class="page-subtitle">Visual representation of incident hotspots in Maramag, Bukidnon</p>
        </div>
        <div class="col-auto">
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="toggleFilters()">
                    <i class="fas fa-filter me-2"></i>Filters
                </button>
                <button class="btn btn-outline-primary" onclick="refreshMap()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
                <a href="{{ route('incidents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Report Incident
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Panel (Initially Hidden) -->
    <div class="row mb-3" id="filterPanel" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Map Filters</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Incident Type</label>
                            <select class="form-select" id="incidentTypeFilter">
                                <option value="">All Types</option>
                                <option value="vehicle_vs_vehicle">Vehicle vs Vehicle</option>
                                <option value="vehicle_vs_pedestrian">Vehicle vs Pedestrian</option>
                                <option value="vehicle_vs_animals">Vehicle vs Animals</option>
                                <option value="vehicle_vs_property">Vehicle vs Property</option>
                                <option value="vehicle_alone">Vehicle Alone</option>
                                <option value="maternity">Maternity</option>
                                <option value="stabbing_shooting">Stabbing/Shooting</option>
                                <option value="transport_to_hospital">Transport to Hospital</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="startDateFilter">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" id="endDateFilter">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Actions</label>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fas fa-search me-1"></i>Apply
                                </button>
                                <button class="btn btn-outline-secondary" onclick="clearFilters()">
                                    <i class="fas fa-times me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-map-marker-alt fa-2x text-danger mb-2"></i>
                    <h4 class="mb-1" id="totalIncidents">{{ $totalIncidents ?? 0 }}</h4>
                    <p class="text-muted mb-0">Total Incidents</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-fire fa-2x text-warning mb-2"></i>
                    <h4 class="mb-1" id="hotspotCount">0</h4>
                    <p class="text-muted mb-0">Active Hotspots</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2" style="color: var(--primary-500);"></i>
                    <h4 class="mb-1" id="highRiskAreas">0</h4>
                    <p class="text-muted mb-0">High Risk Areas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 bg-light">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x text-info mb-2"></i>
                    <h4 class="mb-1" id="recentIncidents">0</h4>
                    <p class="text-muted mb-0">Last 7 Days</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="row">
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-map me-2"></i>Incident Heat Map - Maramag, Bukidnon</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleHeatLayer()" id="heatLayerToggle">
                            <i class="fas fa-eye me-1"></i>Hide Heat
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="toggleMarkers()" id="markersToggle">
                            <i class="fas fa-map-marker-alt me-1"></i>Hide Markers
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="map" style="height: 500px; width: 100%;"></div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Legend:</strong>
                            <span class="badge bg-primary ms-2">Low Density</span>
                            <span class="badge bg-warning ms-1">Medium Density</span>
                            <span class="badge bg-danger ms-1">High Density</span>
                        </div>
                        <small class="text-muted">Last updated: <span id="lastUpdated">{{ now()->format('M d, Y H:i') }}</span></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel - Incident Details -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Incident Details</h6>
                </div>
                <div class="card-body" id="incidentDetails">
                    <div class="text-center text-muted">
                        <i class="fas fa-mouse-pointer fa-2x mb-3"></i>
                        <p>Click on a map marker to view incident details</p>
                    </div>
                </div>
            </div>

            <!-- Recent Incidents List -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Incidents</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    <div id="recentIncidentsList">
                        <div class="text-center text-muted">
                            <i class="fas fa-spinner fa-spin mb-2"></i>
                            <p>Loading recent incidents...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('incidents.create') }}" class="btn btn-danger btn-sm">
                            <i class="fas fa-plus me-2"></i>Report New Incident
                        </a>
                        <a href="{{ route('incidents.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list me-2"></i>View All Incidents
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="exportMapData()">
                            <i class="fas fa-download me-2"></i>Export Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Incident Detail Modal -->
<div class="modal fade" id="incidentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="incidentModalTitle">Incident Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="incidentModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="viewIncidentBtn">View Full Details</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js" />

<style>
.leaflet-container {
    border-radius: 0.375rem;
}

.incident-marker {
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.incident-popup {
    font-size: 0.9rem;
}

.incident-popup .badge {
    font-size: 0.75rem;
}

#map {
    position: relative;
    z-index: 1;
}

.legend-item {
    display: inline-block;
    margin-right: 10px;
}

.legend-color {
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    margin-right: 5px;
    vertical-align: middle;
}
</style>
@endsection

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>
let map;
let heatLayer;
let markersGroup;
let incidentData = [];
let showHeatLayer = true;
let showMarkers = true;

// Maramag, Bukidnon coordinates
const MARAMAG_CENTER = [8.1597, 125.0623];
const DEFAULT_ZOOM = 13;

// Initialize map
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    loadIncidentData();
    loadRecentIncidents();
});

function initializeMap() {
    // Create map centered on Maramag, Bukidnon
    map = L.map('map').setView(MARAMAG_CENTER, DEFAULT_ZOOM);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: ' OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(map);

    // Initialize marker group
    markersGroup = L.layerGroup().addTo(map);

    // Add map controls
    L.control.scale().addTo(map);
}

function loadIncidentData() {
    showLoading();

    fetch('/api/incidents/heat-map', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            incidentData = data.data;
            updateMap();
            updateStatistics();
        } else {
            showError('Failed to load incident data');
        }
    })
    .catch(error => {
        console.error('Error loading incident data:', error);
        showError('Error loading incident data');
    })
    .finally(() => {
        hideLoading();
    });
}

function updateMap() {
    // Clear existing layers
    if (heatLayer) {
        map.removeLayer(heatLayer);
    }
    markersGroup.clearLayers();

    if (incidentData.length === 0) {
        return;
    }

    // Prepare heat map data
    const heatData = incidentData.map(incident => [
        incident.lat,
        incident.lng,
        getSeverityWeight(incident.severity)
    ]);

    // Create heat layer
    if (showHeatLayer) {
        heatLayer = L.heatLayer(heatData, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: {
                0.0: 'blue',
                0.3: 'cyan',
                0.5: 'lime',
                0.7: 'yellow',
                1.0: 'red'
            }
        }).addTo(map);
    }

    // Add individual markers
    if (showMarkers) {
        incidentData.forEach(incident => {
            const marker = L.circleMarker([incident.lat, incident.lng], {
                radius: 8,
                fillColor: getSeverityColor(incident.severity),
                color: 'white',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            });

            // Create popup content
            const popupContent = createPopupContent(incident);
            marker.bindPopup(popupContent);

            // Add click event
            marker.on('click', function() {
                showIncidentDetails(incident);
            });

            markersGroup.addLayer(marker);
        });
    }
}

function createPopupContent(incident) {
    const typeLabel = incident.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    const severityBadge = getSeverityBadge(incident.severity);

    return `
        <div class="incident-popup">
            <strong>${typeLabel}</strong><br>
            <small class="text-muted">${incident.date}</small><br>
            ${severityBadge}
            <hr class="my-2">
            <button class="btn btn-sm btn-primary" onclick="viewIncidentDetails('${incident.id}')">
                <i class="fas fa-eye me-1"></i>View Details
            </button>
        </div>
    `;
}

function getSeverityWeight(severity) {
    const weights = {
        'critical': 1.0,
        'severe': 0.8,
        'moderate': 0.6,
        'minor': 0.4
    };
    return weights[severity] || 0.5;
}

function getSeverityColor(severity) {
    const colors = {
        'critical': '#dc3545',
        'severe': '#fd7e14',
        'moderate': '#ffc107',
        'minor': '#198754'
    };
    return colors[severity] || '#6c757d';
}

function getSeverityBadge(severity) {
    const badges = {
        'critical': '<span class="badge bg-danger">Critical</span>',
        'severe': '<span class="badge bg-warning">Severe</span>',
        'moderate': '<span class="badge bg-info">Moderate</span>',
        'minor': '<span class="badge bg-success">Minor</span>'
    };
    return badges[severity] || '<span class="badge bg-secondary">Unknown</span>';
}

function updateStatistics() {
    document.getElementById('totalIncidents').textContent = incidentData.length;

    // Calculate hotspots (areas with multiple incidents within 500m)
    const hotspots = calculateHotspots(incidentData);
    document.getElementById('hotspotCount').textContent = hotspots.length;

    // High risk areas (critical/severe incidents)
    const highRisk = incidentData.filter(i => ['critical', 'severe'].includes(i.severity));
    document.getElementById('highRiskAreas').textContent = highRisk.length;

    // Recent incidents (last 7 days)
    const lastWeek = new Date();
    lastWeek.setDate(lastWeek.getDate() - 7);
    const recent = incidentData.filter(i => new Date(i.date) >= lastWeek);
    document.getElementById('recentIncidents').textContent = recent.length;
}

function calculateHotspots(incidents) {
    // Simple hotspot calculation - areas with 3+ incidents within 500m
    const hotspots = [];
    const processed = new Set();

    incidents.forEach((incident, index) => {
        if (processed.has(index)) return;

        const nearby = incidents.filter((other, otherIndex) => {
            if (index === otherIndex || processed.has(otherIndex)) return false;
            const distance = calculateDistance(incident.lat, incident.lng, other.lat, other.lng);
            return distance <= 0.5; // 500m
        });

        if (nearby.length >= 2) { // 3+ incidents total (including current)
            hotspots.push({
                center: [incident.lat, incident.lng],
                count: nearby.length + 1,
                incidents: [incident, ...nearby]
            });

            processed.add(index);
            nearby.forEach((_, i) => processed.add(i));
        }
    });

    return hotspots;
}

function calculateDistance(lat1, lng1, lat2, lng2) {
    // Haversine formula for distance in kilometers
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function loadRecentIncidents() {
    fetch('/api/incidents?limit=10', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRecentIncidents(data.data);
        }
    })
    .catch(error => {
        console.error('Error loading recent incidents:', error);
        document.getElementById('recentIncidentsList').innerHTML =
            '<p class="text-muted text-center">Error loading incidents</p>';
    });
}

function displayRecentIncidents(incidents) {
    const container = document.getElementById('recentIncidentsList');

    if (incidents.length === 0) {
        container.innerHTML = '<p class="text-muted text-center">No recent incidents</p>';
        return;
    }

    const html = incidents.map(incident => `
        <div class="border-bottom pb-2 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <small class="text-muted">${incident.incident_number}</small>
                    <p class="mb-1 small">${incident.incident_type.replace(/_/g, ' ')}</p>
                    <small class="text-muted">${incident.location}</small>
                </div>
                <small class="text-muted">${new Date(incident.created_at).toLocaleDateString()}</small>
            </div>
        </div>
    `).join('');

    container.innerHTML = html;
}

function showIncidentDetails(incident) {
    const detailsHtml = `
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Incident Information</h6>
                <p><strong>Type:</strong> ${incident.type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}</p>
                <p><strong>Severity:</strong> ${getSeverityBadge(incident.severity)}</p>
                <p><strong>Date:</strong> ${incident.date}</p>
                <p><strong>Coordinates:</strong> ${incident.lat}, ${incident.lng}</p>
                <hr>
                <div class="d-grid">
                    <a href="/incidents/${incident.id}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>View Full Details
                    </a>
                </div>
            </div>
        </div>
    `;

    document.getElementById('incidentDetails').innerHTML = detailsHtml;
}

// Filter and control functions
function toggleFilters() {
    const panel = document.getElementById('filterPanel');
    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
}

function toggleHeatLayer() {
    showHeatLayer = !showHeatLayer;
    const button = document.getElementById('heatLayerToggle');

    if (showHeatLayer) {
        button.innerHTML = '<i class="fas fa-eye me-1"></i>Hide Heat';
    } else {
        button.innerHTML = '<i class="fas fa-eye-slash me-1"></i>Show Heat';
        if (heatLayer) {
            map.removeLayer(heatLayer);
        }
    }

    updateMap();
}

function toggleMarkers() {
    showMarkers = !showMarkers;
    const button = document.getElementById('markersToggle');

    if (showMarkers) {
        button.innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>Hide Markers';
    } else {
        button.innerHTML = '<i class="fas fa-map-marker me-1"></i>Show Markers';
        markersGroup.clearLayers();
    }

    updateMap();
}

function applyFilters() {
    const incidentType = document.getElementById('incidentTypeFilter').value;
    const startDate = document.getElementById('startDateFilter').value;
    const endDate = document.getElementById('endDateFilter').value;

    // Build query parameters
    const params = new URLSearchParams();
    if (incidentType) params.append('incident_type', incidentType);
    if (startDate) params.append('start_date', startDate);
    if (endDate) params.append('end_date', endDate);

    // Fetch filtered data
    fetch(`/api/incidents/heat-map?${params.toString()}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            incidentData = data.data;
            updateMap();
            updateStatistics();
        }
    })
    .catch(error => {
        console.error('Error applying filters:', error);
        showError('Error applying filters');
    });
}

function clearFilters() {
    document.getElementById('incidentTypeFilter').value = '';
    document.getElementById('startDateFilter').value = '';
    document.getElementById('endDateFilter').value = '';
    loadIncidentData();
}

function refreshMap() {
    loadIncidentData();
    loadRecentIncidents();
    document.getElementById('lastUpdated').textContent = new Date().toLocaleString();
}

function exportMapData() {
    // Simple CSV export
    const csvData = incidentData.map(incident =>
        `"${incident.type}","${incident.severity}","${incident.lat}","${incident.lng}","${incident.date}"`
    ).join('\n');

    const header = '"Type","Severity","Latitude","Longitude","Date"\n';
    const csv = header + csvData;

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = `incident_data_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
}

// Utility functions
function showLoading() {
    // You can implement a loading overlay here
}

function hideLoading() {
    // Hide loading overlay
}

function showError(message) {
    console.error(message);
    // You can implement error toast notification here
}
</script>
@endsection
