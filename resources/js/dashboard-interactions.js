document.addEventListener('DOMContentLoaded', function () {

    var searchInput = document.getElementById('activitySearch');
    var activityItems = document.querySelectorAll('.activity-item');
    var searchEmptyMsg = document.getElementById('activitySearchEmpty');

    if (searchInput && activityItems.length) {
        searchInput.addEventListener('input', function () {
            var query = searchInput.value.trim().toLowerCase();
            var visibleCount = 0;

            activityItems.forEach(function (item) {
                var matches = item.textContent.toLowerCase().includes(query);
                item.classList.toggle('is-hidden', !matches);
                if (matches) visibleCount++;
            });

            if (searchEmptyMsg) {
                searchEmptyMsg.classList.toggle('is-visible', visibleCount === 0 && query !== '');
            }
        });
    }

    var trafficCols = document.querySelectorAll('.traffic-chart__col');
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