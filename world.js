document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('country');
    const result = document.getElementById('result');
    const lookupBtn = document.getElementById('lookup');
    const citiesBtn = document.getElementById('cities');

    // Sends the AJAX request and renders the returned HTML
    async function fetchData(params) {
        const query = new URLSearchParams(params).toString();
        result.innerHTML = '<p class="status">Loading...</p>';

        try {
            const response = await fetch(`world.php?${query}`);
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            result.innerHTML = await response.text();
        } catch (error) {
            console.error(error);
            result.innerHTML = '<p class="error">Something went wrong. Please try again.</p>';
        }
    }

    lookupBtn.addEventListener('click', (e) => {
        e.preventDefault();
        fetchData({ country: input.value.trim() });
    });

    // Same search, but asks world.php for cities instead
    if (citiesBtn) {
        citiesBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fetchData({ country: input.value.trim(), lookup: 'cities' });
        });
    }

    // Pressing Enter triggers a country lookup
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            lookupBtn.click();
        }
    });
});