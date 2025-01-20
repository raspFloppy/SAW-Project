<?php

require_once 'database.php';

class CoursesController
{

    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function get_courses(): array
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM Course");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return ['success' => true, 'courses' => $courses];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function get_course(int $id): array
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Course WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$course) {
                return ['success' => false, 'message' => 'Course not found'];
            }
            return ['success' => true, 'course' => $course];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
