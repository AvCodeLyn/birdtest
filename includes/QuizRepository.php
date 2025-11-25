<?php

class QuizRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getActiveQuiz(): ?array
    {
        $stmt = $this->pdo->query('SELECT * FROM quizzes WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1');
        $quiz = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $quiz ?: null;
    }

    public function getQuestionsWithAnswers(int $quizId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT q.id AS question_id, q.content AS question_content, q.position AS question_position, '
            . 'a.label, a.content AS answer_content, a.position AS answer_position '
            . 'FROM questions q '
            . 'JOIN answers a ON a.question_id = q.id '
            . 'WHERE q.quiz_id = :quizId '
            . 'ORDER BY q.position ASC, a.position ASC'
        );
        $stmt->execute(['quizId' => $quizId]);

        $grouped = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $questionId = (int) $row['question_id'];
            if (!isset($grouped[$questionId])) {
                $grouped[$questionId] = [
                    'id' => $questionId,
                    'content' => $row['question_content'],
                    'answers' => [],
                    'position' => (int) $row['question_position'],
                ];
            }

            $grouped[$questionId]['answers'][$row['label']] = $row['answer_content'];
        }

        usort($grouped, fn($a, $b) => $a['position'] <=> $b['position']);

        return array_values($grouped);
    }

    public function createQuestion(int $quizId, string $content, array $answers, ?int $position = null): int
    {
        $this->pdo->beginTransaction();

        try {
            if ($position === null) {
                $stmt = $this->pdo->prepare('SELECT COALESCE(MAX(position), 0) + 1 FROM questions WHERE quiz_id = :quizId');
                $stmt->execute(['quizId' => $quizId]);
                $position = (int) $stmt->fetchColumn();
            }

            $questionStmt = $this->pdo->prepare(
                'INSERT INTO questions (quiz_id, content, position) VALUES (:quizId, :content, :position)'
            );
            $questionStmt->execute([
                'quizId' => $quizId,
                'content' => $content,
                'position' => $position,
            ]);

            $questionId = (int) $this->pdo->lastInsertId();

            $answerStmt = $this->pdo->prepare(
                'INSERT INTO answers (question_id, label, content, position) VALUES (:questionId, :label, :content, :position)'
            );

            $order = 1;
            foreach ($answers as $label => $answerContent) {
                $answerStmt->execute([
                    'questionId' => $questionId,
                    'label' => $label,
                    'content' => $answerContent,
                    'position' => $order++,
                ]);
            }

            $this->pdo->commit();

            return $questionId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function updateQuestion(int $questionId, string $content, array $answers, ?int $position = null): void
    {
        $this->pdo->beginTransaction();

        try {
            $updateQuestionSql = 'UPDATE questions SET content = :content';
            $params = [
                'content' => $content,
                'questionId' => $questionId,
            ];

            if ($position !== null) {
                $updateQuestionSql .= ', position = :position';
                $params['position'] = $position;
            }

            $updateQuestionSql .= ' WHERE id = :questionId';

            $questionStmt = $this->pdo->prepare($updateQuestionSql);
            $questionStmt->execute($params);

            $this->pdo->prepare('DELETE FROM answers WHERE question_id = :questionId')->execute([
                'questionId' => $questionId,
            ]);

            $answerStmt = $this->pdo->prepare(
                'INSERT INTO answers (question_id, label, content, position) VALUES (:questionId, :label, :content, :position)'
            );

            $order = 1;
            foreach ($answers as $label => $answerContent) {
                $answerStmt->execute([
                    'questionId' => $questionId,
                    'label' => $label,
                    'content' => $answerContent,
                    'position' => $order++,
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deleteQuestion(int $questionId): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->pdo->prepare('DELETE FROM answers WHERE question_id = :questionId')->execute([
                'questionId' => $questionId,
            ]);

            $this->pdo->prepare('DELETE FROM questions WHERE id = :questionId')->execute([
                'questionId' => $questionId,
            ]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
