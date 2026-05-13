<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($q)) {
    echo json_encode(['success' => false, 'message' => 'Query is empty']);
    exit;
}

try {
    // Search query: title matches first, then writer (author) matches
    // Using actual column names from setup.sql: title, writer, description, last_edited, cover_image, reading_time
    $stmt = $pdo->prepare("
        SELECT *, 
               CASE 
                   WHEN title LIKE :term_title THEN 1 
                   ELSE 2 
               END as relevance
        FROM articles 
        WHERE title LIKE :term OR writer LIKE :term 
        ORDER BY relevance ASC, title ASC
    ");
    
    $term = "%$q%";
    $term_title = "%$q%";
    $stmt->execute([
        'term' => $term,
        'term_title' => $term_title
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the date for display if needed, though we can do it in JS too
    // But let's keep it consistent with what articles.php expects
    foreach ($results as &$article) {
        $article['formatted_date'] = date('F d, Y', strtotime($article['last_edited']));
        // Strip tags from description for the excerpt
        $article['excerpt'] = mb_substr(strip_tags($article['description']), 0, 300) . '...';
    }

    echo json_encode([
        'success' => true,
        'results' => $results,
        'count' => count($results)
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
