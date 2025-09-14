       </main>

<footer class="text-center py-6 text-gray-400 border-t border-gray-600">
    <p>&copy; <?= date("Y") ?> CineMatch. All rights reserved.</p>
</footer>

<script>
    AOS.init({
        duration: 1000,
        once: true,
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('search-input');
        const suggestionsDiv = document.getElementById('suggestions');
        let selectedIndex = -1;
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const query = this.value.trim();
                    if (query.length > 0) {
                        fetch(`/cinematch/search_suggestions.php?query=${encodeURIComponent(query)}`)
                            .then(res => res.json())
                            .then(data => {
                                suggestionsDiv.innerHTML = '';
                                selectedIndex = -1;

                                if (data.length > 0) {
                                    data.forEach((movie, index) => {
                                        const div = document.createElement('div');
                                        div.className = 'flex items-center gap-2 px-4 py-2 hover:bg-primary cursor-pointer';
                                        div.innerHTML = `<img src="${movie.poster}" class="w-8 h-12 object-cover rounded-sm" alt="${movie.title}">
                                            <span>${movie.title}</span>`;

                                        div.addEventListener('click', function () {
                                            searchInput.value = movie.title;
                                            suggestionsDiv.classList.add('hidden');
                                        });

                                        suggestionsDiv.appendChild(div);
                                    });

                                    suggestionsDiv.classList.remove('hidden');
                                } else {
                                    suggestionsDiv.classList.add('hidden');
                                }
                            });
                    } else {
                        suggestionsDiv.classList.add('hidden');
                    }
                }, 300);
            });

            // Keyboard navigation
            searchInput.addEventListener('keydown', function (e) {
                const items = suggestionsDiv.querySelectorAll('div');
                if (items.length === 0) return;

                if (e.key === 'ArrowDown') {
                    selectedIndex = (selectedIndex + 1) % items.length;
                    items.forEach((item, idx) => item.classList.toggle('bg-primary', idx === selectedIndex));
                    e.preventDefault();
                } else if (e.key === 'ArrowUp') {
                    selectedIndex = (selectedIndex - 1 + items.length) % items.length;
                    items.forEach((item, idx) => item.classList.toggle('bg-primary', idx === selectedIndex));
                    e.preventDefault();
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0) {
                        searchInput.value = items[selectedIndex].innerText;
                        suggestionsDiv.classList.add('hidden');
                        selectedIndex = -1;
                        e.preventDefault();
                    }
                }
            });

            // Hide suggestions when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
                    suggestionsDiv.classList.add('hidden');
                    selectedIndex = -1;
                }
            });
        }
    });
</script>

</body>

</html>
