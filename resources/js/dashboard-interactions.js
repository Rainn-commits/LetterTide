document.addEventListener('DOMContentLoaded', function () {

    var searchInput = document.getElementById('activitySearch');
    var activityItems = document.querySelectorAll('.activity-feed__item');
    var searchEmptyMsg = document.getElementById('activitySearchEmpty');

    if (searchInput && activityItems.length) {
        searchInput.addEventListener('input', function () {
            var query = searchInput.value.trim().toLowerCase();
            var visibleCount = 0;

            activityItems.forEach(function (item) {
                var matches = item.textContent.toLowerCase().includes(query);
                
                if (matches) {
                    item.style.display = 'flex';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            if (searchEmptyMsg) {
                searchEmptyMsg.style.display = (visibleCount === 0 && query !== '') ? 'block' : 'none';
            }
        });
    }

    var trafficCols = document.querySelectorAll('.traffic-chart-pro__col');
    var trafficDetail = document.getElementById('trafficDetail');

    if (trafficCols.length && trafficDetail) {
        trafficCols.forEach(function (col) {
            col.addEventListener('click', function () {
                trafficCols.forEach(function (c) { c.classList.remove('is-selected'); });
                col.classList.add('is-selected');

                var label = col.getAttribute('data-label');
                var count = col.getAttribute('data-count');

                trafficDetail.innerHTML = 'Bulan <strong>' + label + '</strong>: <strong>' + count + ' surat</strong> dibuat.';
            });
        });
    }

});
