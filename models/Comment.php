<?php
class Comment
{
    public $id;
    public $user_id;
    public $comment_type; // 'post' hoặc 'product'
    public $product_id;
    public $post_id;
    public $content;
    public $rating;
    public $created_at;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->comment_type = $data['comment_type'] ?? 'post';
        $this->product_id = $data['product_id'] ?? null;
        $this->post_id = $data['post_id'] ?? null;
        $this->content = $data['content'] ?? '';
        $this->rating = $data['rating'] ?? null;
        $this->created_at = $data['created_at'] ?? date('Y-m-d H:i:s');
    }
}
