<?php
/**
 * GoogleBooks class
 * Wraps calls to the Google Books API for searching and importing book data.
 */
class GoogleBooks
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = GOOGLE_BOOKS_API_URL;
        $this->apiKey = GOOGLE_BOOKS_API_KEY;
    }

    /**
     * Search Google Books by free-text query. Returns a normalized array of results.
     */
    public function search(string $query, int $maxResults = 12): array
    {
        $params = [
            'q'          => $query,
            'maxResults' => $maxResults,
        ];
        if ($this->apiKey !== '') {
            $params['key'] = $this->apiKey;
        }
        $url = $this->apiUrl . '?' . http_build_query($params);

        $response = $this->httpGet($url);
        if ($response === null) {
            return [];
        }
        $data = json_decode($response, true);
        if (empty($data['items'])) {
            return [];
        }

        $results = [];
        foreach ($data['items'] as $item) {
            $results[] = $this->normalize($item);
        }
        return $results;
    }

    public function getByVolumeId(string $volumeId): ?array
    {
        $url = $this->apiUrl . '/' . urlencode($volumeId) . ($this->apiKey !== '' ? '?key=' . $this->apiKey : '');
        $response = $this->httpGet($url);
        if ($response === null) return null;
        $item = json_decode($response, true);
        return $item ? $this->normalize($item) : null;
    }

    private function normalize(array $item): array
    {
        $info = $item['volumeInfo'] ?? [];
        $identifiers = $info['industryIdentifiers'] ?? [];
        $isbn = '';
        foreach ($identifiers as $id) {
            if (in_array($id['type'], ['ISBN_13', 'ISBN_10'], true)) {
                $isbn = $id['identifier'];
                if ($id['type'] === 'ISBN_13') break;
            }
        }

        return [
            'volume_id'      => $item['id'] ?? null,
            'title'          => $info['title'] ?? 'Untitled',
            'author'         => isset($info['authors']) ? implode(', ', $info['authors']) : 'Unknown',
            'publisher'      => $info['publisher'] ?? '',
            'description'    => $info['description'] ?? '',
            'isbn'           => $isbn,
            'published_date' => $info['publishedDate'] ?? '',
            'categories'     => isset($info['categories']) ? implode(', ', $info['categories']) : '',
            'thumbnail'      => str_replace('http://', 'https://', $info['imageLinks']['thumbnail'] ?? ''),
            'language'       => strtoupper($info['language'] ?? 'en'),
            'page_count'     => $info['pageCount'] ?? null,
        ];
    }

    /** Download a cover image (from Google's thumbnail URL) into local uploads folder */
    public function downloadCover(string $thumbnailUrl): ?string
    {
        if ($thumbnailUrl === '') return null;
        $data = $this->httpGet($thumbnailUrl);
        if ($data === null) return null;

        $filename = 'gbooks_' . uniqid() . '.jpg';
        $dest = rtrim(UPLOAD_COVER_DIR, '/') . '/' . $filename;
        file_put_contents($dest, $data);
        return $filename;
    }

    private function httpGet(string $url): ?string
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => "Mozilla/5.0",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json"
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode == 429) {
        die("Google Books API Rate Limit Reached (429). Try again later or use an API Key.");
    }

    if ($httpCode != 200) {
        return null;
    }

    return $response;
}
}