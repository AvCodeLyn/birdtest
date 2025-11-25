<?php

class QuizRepository
{
    private $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    public function getQuizzes(): array
    {
        $result = $this->conn->query("SELECT * FROM quizzes ORDER BY created_at DESC");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getQuiz(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM quizzes WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $quiz = $result->fetch_assoc();
        $stmt->close();

        return $quiz ?: null;
    }

    public function createQuiz(string $title, ?string $description): int
    {
        $stmt = $this->conn->prepare("INSERT INTO quizzes (title, description) VALUES (?, ?)");
        $stmt->bind_param('ss', $title, $description);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function updateQuiz(int $id, string $title, ?string $description): void
    {
        $stmt = $this->conn->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
        $stmt->bind_param('ssi', $title, $description, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function getQuestions(int $quizId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->bind_param('i', $quizId);
        $stmt->execute();
        $result = $stmt->get_result();
        $questions = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $questions;
    }

    public function getQuestion(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM quiz_questions WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $question = $result->fetch_assoc();
        $stmt->close();

        return $question ?: null;
    }

    public function createQuestion(int $quizId, string $text, string $type, int $order): int
    {
        $stmt = $this->conn->prepare("INSERT INTO quiz_questions (quiz_id, text, type, sort_order) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('issi', $quizId, $text, $type, $order);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function updateQuestion(int $id, string $text, string $type, int $order): void
    {
        $stmt = $this->conn->prepare("UPDATE quiz_questions SET text = ?, type = ?, sort_order = ? WHERE id = ?");
        $stmt->bind_param('ssii', $text, $type, $order, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function getAnswers(int $questionId): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM quiz_answers WHERE question_id = ? ORDER BY id ASC");
        $stmt->bind_param('i', $questionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $answers = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $answers;
    }

    public function createAnswer(int $questionId, string $content, int $weight): int
    {
        $stmt = $this->conn->prepare("INSERT INTO quiz_answers (question_id, content, weight) VALUES (?, ?, ?)");
        $stmt->bind_param('isi', $questionId, $content, $weight);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

    public function updateAnswer(int $id, string $content, int $weight): void
    {
        $stmt = $this->conn->prepare("UPDATE quiz_answers SET content = ?, weight = ? WHERE id = ?");
        $stmt->bind_param('sii', $content, $weight, $id);
        $stmt->execute();
        $stmt->close();
    }
}

