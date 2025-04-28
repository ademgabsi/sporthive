// assets/js/map.js

document.addEventListener('DOMContentLoaded', function() {
  console.log('Map script loaded');

  const mapElement = document.getElementById('map');
  if (!mapElement) {
    console.error('Map element not found!');
    return;
  }
  console.log('Map element found:', mapElement);

  const input = document.querySelector('[name="terrain[localisation]"]');
  if (!input) {
    console.error('Localisation input not found!');
  } else {
    console.log('Localisation input found:', input.value);
  }

  const searchInput = document.getElementById('location-search');
  const searchButton = document.getElementById('search-button');

  // Make sure Leaflet is loaded
  if (typeof L === 'undefined') {
    console.error('Leaflet library not loaded!');
    return;
  }

  console.log('Initializing map...');
  // Initialize the map with a default view of Tunisia
  const map = L.map(mapElement).setView([36.8065, 10.1815], 13);

  // Add the base map layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
  }).addTo(map);

  // Add scale control
  L.control.scale().addTo(map);

  let marker = null;

  // Function to update marker position
  function updateMarkerPosition(latlng) {
    if(marker) map.removeLayer(marker);
    marker = L.marker(latlng, {draggable: true}).addTo(map);

    // Update input value when marker is placed
    input.value = `${latlng.lat.toFixed(6)},${latlng.lng.toFixed(6)}`;

    // Update input value when marker is dragged
    marker.on('dragend', function(e) {
      const position = marker.getLatLng();
      input.value = `${position.lat.toFixed(6)},${position.lng.toFixed(6)}`;
    });
  }

  // Handle map click to place marker
  map.on('click', function(e) {
    updateMarkerPosition(e.latlng);
  });

  // Initialize with existing value
  if(input.value) {
    const [lat, lng] = input.value.split(',');
    if(!isNaN(lat) && !isNaN(lng)) {
      const latlng = L.latLng(parseFloat(lat), parseFloat(lng));
      updateMarkerPosition(latlng);
      map.setView(latlng, 15);
    }
  }

  // Handle location search if search elements exist
  if(searchInput && searchButton) {
    searchButton.addEventListener('click', function() {
      searchLocation();
    });

    searchInput.addEventListener('keypress', function(e) {
      if(e.key === 'Enter') {
        e.preventDefault();
        searchLocation();
      }
    });

    function searchLocation() {
      const query = searchInput.value.trim();
      if(query.length > 0) {
        // Use Nominatim for geocoding (OpenStreetMap's geocoding service)
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
          .then(response => response.json())
          .then(data => {
            if(data && data.length > 0) {
              const result = data[0];
              const latlng = L.latLng(parseFloat(result.lat), parseFloat(result.lon));
              updateMarkerPosition(latlng);
              map.setView(latlng, 15);
            } else {
              alert('Aucun résultat trouvé pour cette recherche.');
            }
          })
          .catch(error => {
            console.error('Erreur de recherche:', error);
            alert('Erreur lors de la recherche. Veuillez réessayer.');
          });
      }
    }
  }
});