--comentarios de usuarios
SELECT 
    u.id as user_id,
    u.name as user_name,
    c.id as card_id,
    c.title as card_title,
    com.id as comment_id,
    com.content as comment_content
FROM users u
JOIN cards c ON u.id = c.user_id
JOIN comments com ON c.id = com.card_id
ORDER BY u.id, c.id, com.id;