// One request per icon folder per page, shared by every Iconamic field. The URL identifies the folder (its settings
// are encrypted into it by the fieldtype's preload), so fields using the same folder share the same promise.
const cache = new Map();

export function loadIcons(url) {
    if (!cache.has(url)) {
        const request = fetch(url, {
            credentials: 'same-origin',
            headers: {Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest'},
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`Iconamic: loading icons failed (${response.status})`);
                }
                return response.json();
            })
            .then((data) => data.icons ?? {});

        // A failed request should not poison the cache for the rest of the page.
        request.catch(() => cache.delete(url));
        cache.set(url, request);
    }

    return cache.get(url);
}
