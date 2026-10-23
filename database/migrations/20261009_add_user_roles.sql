-- Execute uma única vez em bancos criados antes do suporte a professores.
ALTER TABLE `users`
    ADD COLUMN `tipo_usuario` ENUM('aluno','professor') NOT NULL DEFAULT 'aluno'
        AFTER `senha`,
    MODIFY COLUMN `serie` ENUM('6°','7°','8°','9°') NULL;
