-- ============================================================
--  MathPlay Solutions — Banco de Dados Completo
--  Studio Game Over
--  Charset: utf8mb4 | Engine: InnoDB
-- ============================================================

CREATE DATABASE IF NOT EXISTS `mathplay`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `mathplay`;

-- ============================================================
--  TABELAS
-- ============================================================

-- Usuários
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `nome`       VARCHAR(100)     NOT NULL,
    `username`   VARCHAR(50)      NOT NULL UNIQUE,
    `email`      VARCHAR(150)     NOT NULL UNIQUE,
    `senha`      VARCHAR(255)     NOT NULL,
    `serie`      ENUM('6°','7°','8°','9°') NOT NULL,
    `xp`         INT UNSIGNED     NOT NULL DEFAULT 0,
    `nivel`      TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `pontuacao`  INT UNSIGNED     NOT NULL DEFAULT 0,
    `avatar`     VARCHAR(50)      NOT NULL DEFAULT 'avatar1',
    `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Questões
CREATE TABLE IF NOT EXISTS `questions` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `jogo`            ENUM('matematica','portugues') NOT NULL,
    `categoria`       VARCHAR(50)  NOT NULL,
    `dificuldade`     ENUM('facil','medio','dificil') NOT NULL,
    `pergunta`        TEXT         NOT NULL,
    `alternativa_a`   VARCHAR(255) NOT NULL,
    `alternativa_b`   VARCHAR(255) NOT NULL,
    `alternativa_c`   VARCHAR(255) NOT NULL,
    `alternativa_d`   VARCHAR(255) NOT NULL,
    `resposta_correta` ENUM('a','b','c','d') NOT NULL,
    `dica`            TEXT         NOT NULL,
    `explicacao`      TEXT         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Partidas
CREATE TABLE IF NOT EXISTS `matches` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED NOT NULL,
    `jogo`         VARCHAR(50)  NOT NULL,
    `dificuldade`  VARCHAR(20)  NOT NULL,
    `pontuacao`    INT UNSIGNED NOT NULL DEFAULT 0,
    `acertos`      INT UNSIGNED NOT NULL DEFAULT 0,
    `erros`        INT UNSIGNED NOT NULL DEFAULT 0,
    `xp_ganho`     INT UNSIGNED NOT NULL DEFAULT 0,
    `data_partida` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_matches_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conquistas
CREATE TABLE IF NOT EXISTS `achievements` (
    `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`      VARCHAR(100) NOT NULL,
    `descricao` VARCHAR(255) NOT NULL,
    `icone`     VARCHAR(10)  NOT NULL,
    `requisito` VARCHAR(100) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conquistas dos usuários
CREATE TABLE IF NOT EXISTS `user_achievements` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`         INT UNSIGNED NOT NULL,
    `achievement_id`  INT UNSIGNED NOT NULL,
    `data_conquista`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ua_user`        FOREIGN KEY (`user_id`)        REFERENCES `users`        (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_ua_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Progresso por jogo
CREATE TABLE IF NOT EXISTS `progress` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`          INT UNSIGNED NOT NULL,
    `jogo`             VARCHAR(50)  NOT NULL,
    `melhor_pontuacao` INT UNSIGNED NOT NULL DEFAULT 0,
    `partidas`         INT UNSIGNED NOT NULL DEFAULT 0,
    `acertos`          INT UNSIGNED NOT NULL DEFAULT 0,
    `erros`            INT UNSIGNED NOT NULL DEFAULT 0,
    `progresso`        TINYINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_progress_user_jogo` (`user_id`, `jogo`),
    CONSTRAINT `fk_progress_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  CONQUISTAS
-- ============================================================

INSERT INTO `achievements` (`id`, `nome`, `descricao`, `icone`, `requisito`) VALUES
(1, 'Primeiro Desafio',       'Complete sua primeira partida',                      '🏅', 'primeira_partida'),
(2, 'Sequência de Acertos',   'Consiga 5 acertos seguidos em uma partida',          '🔥', 'sequencia_5'),
(3, 'Gênio da Matemática',    'Alcance 500 pontos em Matemática',                   '🧠', 'pontos_matematica_500'),
(4, 'Mestre das Palavras',    'Alcance 500 pontos em Português',                    '📚', 'pontos_portugues_500'),
(5, 'Partida Perfeita',       'Complete uma partida sem errar nenhuma questão',     '💯', 'partida_perfeita'),
(6, 'Campeão',                'Alcance o nível Mestre (nível 5)',                   '🏆', 'nivel_mestre');

-- ============================================================
--  QUESTÕES DE MATEMÁTICA
-- ============================================================

-- -------------------------------------------------------
--  MATEMÁTICA — FÁCIL (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q1
('matematica', 'numeros_inteiros', 'facil',
 'Quanto é 25 + 37?',
 '52', '62', '72', '57',
 'b',
 'Adicione as dezenas e depois as unidades separadamente.',
 '25 + 37: some 20 + 30 = 50; depois 5 + 7 = 12. Total: 50 + 12 = 62.'),

-- Q2
('matematica', 'numeros_inteiros', 'facil',
 'Qual é o resultado de 84 − 39?',
 '55', '45', '35', '43',
 'b',
 'Tente subtrair 40 de 84 e depois corrija somando 1.',
 '84 − 40 = 44; como subtraímos 1 a mais, somamos de volta: 44 + 1 = 45.'),

-- Q3
('matematica', 'numeros_inteiros', 'facil',
 'Quanto é 6 × 7?',
 '36', '48', '42', '56',
 'c',
 'Pense na tabuada do 6: 6, 12, 18, 24, 30, 36, 42...',
 'Na tabuada do 6, o sétimo múltiplo é 42 (6 × 7 = 42).'),

-- Q4
('matematica', 'numeros_inteiros', 'facil',
 'Qual é o resultado de 144 ÷ 12?',
 '10', '14', '12', '11',
 'c',
 'Pense: qual número multiplicado por 12 resulta em 144?',
 '12 × 12 = 144, portanto 144 ÷ 12 = 12.'),

-- Q5
('matematica', 'fracoes', 'facil',
 'Qual fração representa a metade de um inteiro?',
 '1/4', '2/3', '1/2', '3/4',
 'c',
 'Metade significa dividir em 2 partes iguais e pegar 1.',
 'Metade = 1 de 2 partes iguais = 1/2. O numerador representa as partes escolhidas e o denominador, as partes totais.'),

-- Q6
('matematica', 'fracoes', 'facil',
 'Simplifique a fração 4/8.',
 '2/3', '1/3', '1/2', '3/4',
 'c',
 'Divida numerador e denominador pelo maior divisor comum (MDC).',
 'MDC(4, 8) = 4. Então 4 ÷ 4 = 1 e 8 ÷ 4 = 2. Fração simplificada: 1/2.'),

-- Q7
('matematica', 'porcentagem', 'facil',
 'Quanto é 50% de 200?',
 '50', '150', '100', '25',
 'c',
 '50% é a metade. Basta dividir o número por 2.',
 '50% de 200 = 200 ÷ 2 = 100. Percentagem significa "por cem": 50/100 × 200 = 100.'),

-- Q8
('matematica', 'porcentagem', 'facil',
 'Quanto é 10% de 80?',
 '10', '8', '18', '80',
 'b',
 '10% equivale a dividir o número por 10.',
 '10% de 80 = 80 ÷ 10 = 8. Ou ainda: 10/100 × 80 = 0,1 × 80 = 8.'),

-- Q9
('matematica', 'numeros_inteiros', 'facil',
 'A temperatura estava em 5°C e caiu 9 graus. Qual é a nova temperatura?',
 '−4°C', '4°C', '−5°C', '14°C',
 'a',
 'Quando a temperatura cai, subtraímos. Se o resultado for negativo, usamos números negativos.',
 '5 − 9 = −4. A temperatura ficou em −4°C, abaixo de zero.'),

-- Q10
('matematica', 'numeros_inteiros', 'facil',
 'Qual é o dobro de 36?',
 '18', '62', '72', '68',
 'c',
 'Dobro significa multiplicar por 2.',
 '36 × 2 = 72. Você pode calcular 30 × 2 = 60 e 6 × 2 = 12; 60 + 12 = 72.');

-- -------------------------------------------------------
--  MATEMÁTICA — MÉDIO (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q11
('matematica', 'fracoes', 'medio',
 'Qual é o resultado de 1/3 + 1/6?',
 '2/9', '1/2', '2/6', '1/4',
 'b',
 'Para somar frações com denominadores diferentes, encontre o MMC dos denominadores.',
 'MMC(3, 6) = 6. Converta: 1/3 = 2/6. Então 2/6 + 1/6 = 3/6 = 1/2.'),

-- Q12
('matematica', 'fracoes', 'medio',
 'Quanto é 3/4 de 80?',
 '20', '40', '60', '70',
 'c',
 'Multiplique 80 por 3 e depois divida por 4.',
 '80 × 3 = 240; 240 ÷ 4 = 60. Ou calcule 80 ÷ 4 = 20 (um quarto) e depois 20 × 3 = 60.'),

-- Q13
('matematica', 'porcentagem', 'medio',
 'Qual é 25% de 160?',
 '25', '40', '45', '80',
 'b',
 '25% é um quarto. Divida o número por 4.',
 '25% = 1/4. Então 160 ÷ 4 = 40. Confirmando: 25/100 × 160 = 0,25 × 160 = 40.'),

-- Q14
('matematica', 'porcentagem', 'medio',
 'Um produto custava R$ 80,00 e teve um desconto de 20%. Quanto custa agora?',
 'R$ 60,00', 'R$ 72,00', 'R$ 64,00', 'R$ 16,00',
 'c',
 'Calcule 20% de 80 e subtraia do valor original.',
 '20% de 80 = 80 × 0,20 = 16. Valor final: 80 − 16 = R$ 64,00.'),

-- Q15
('matematica', 'equacoes', 'medio',
 'Resolva a equação: 2x + 5 = 17',
 'x = 7', 'x = 6', 'x = 11', 'x = 5',
 'b',
 'Isole o x: passe o +5 para o outro lado subtraindo 5, depois divida por 2.',
 '2x + 5 = 17 → 2x = 17 − 5 → 2x = 12 → x = 12 ÷ 2 → x = 6.'),

-- Q16
('matematica', 'equacoes', 'medio',
 'Qual o valor de x em: 3x − 4 = 11?',
 'x = 3', 'x = 7', 'x = 5', 'x = 9',
 'c',
 'Passe o −4 para o outro lado somando 4, depois divida pelo coeficiente de x.',
 '3x − 4 = 11 → 3x = 11 + 4 → 3x = 15 → x = 15 ÷ 3 → x = 5.'),

-- Q17
('matematica', 'geometria', 'medio',
 'Qual é a área de um quadrado com lado de 9 cm?',
 '36 cm²', '18 cm²', '81 cm²', '45 cm²',
 'c',
 'A área do quadrado é calculada multiplicando o lado por ele mesmo (lado²).',
 'Área = lado × lado = 9 × 9 = 81 cm². Essa é também a definição de potência: 9² = 81.'),

-- Q18
('matematica', 'geometria', 'medio',
 'Um retângulo tem base de 12 cm e altura de 5 cm. Qual é a sua área?',
 '34 cm²', '60 cm²', '17 cm²', '70 cm²',
 'b',
 'Área do retângulo = base × altura.',
 'Área = 12 × 5 = 60 cm². O perímetro seria 2 × (12 + 5) = 34 cm, mas a questão pede área.'),

-- Q19
('matematica', 'numeros_inteiros', 'medio',
 'Qual é o resultado de (−8) × (−5)?',
 '−40', '13', '40', '−13',
 'c',
 'O produto de dois números negativos é sempre positivo.',
 'Regra dos sinais: (−) × (−) = (+). Então 8 × 5 = 40, e o resultado é +40.'),

-- Q20
('matematica', 'problemas', 'medio',
 'João tem R$ 150,00. Gastou 1/3 em livros e 1/5 em lanches. Quanto sobrou?',
 'R$ 60,00', 'R$ 80,00', 'R$ 70,00', 'R$ 90,00',
 'c',
 'Calcule 1/3 de 150 e 1/5 de 150, some os gastos e subtraia do total.',
 '1/3 de 150 = 50; 1/5 de 150 = 30. Gastos: 50 + 30 = 80. Sobrou: 150 − 80 = R$ 70,00.');

-- -------------------------------------------------------
--  MATEMÁTICA — DIFÍCIL (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q21
('matematica', 'potenciacao', 'dificil',
 'Quanto é 2⁵?',
 '10', '16', '32', '64',
 'c',
 'Potenciação é uma multiplicação repetida: 2⁵ = 2 × 2 × 2 × 2 × 2.',
 '2¹ = 2; 2² = 4; 2³ = 8; 2⁴ = 16; 2⁵ = 32. Cada passo multiplica o resultado anterior por 2.'),

-- Q22
('matematica', 'potenciacao', 'dificil',
 'Qual é o valor de √144?',
 '14', '13', '12', '11',
 'c',
 'Pense: qual número multiplicado por si mesmo resulta em 144?',
 '12 × 12 = 144, portanto √144 = 12. A raiz quadrada é a operação inversa da potenciação.'),

-- Q23
('matematica', 'equacoes', 'dificil',
 'Resolva o sistema: x + y = 10 e x − y = 4. Qual é o valor de x?',
 'x = 5', 'x = 8', 'x = 7', 'x = 3',
 'c',
 'Some as duas equações para eliminar y e encontrar x.',
 'Somando: (x + y) + (x − y) = 10 + 4 → 2x = 14 → x = 7. Então y = 10 − 7 = 3.'),

-- Q24
('matematica', 'geometria', 'dificil',
 'Qual é a área de um triângulo com base de 10 cm e altura de 8 cm?',
 '80 cm²', '40 cm²', '18 cm²', '20 cm²',
 'b',
 'Área do triângulo = (base × altura) ÷ 2.',
 'Área = (10 × 8) ÷ 2 = 80 ÷ 2 = 40 cm². O denominador 2 aparece porque o triângulo é metade de um paralelogramo.'),

-- Q25
('matematica', 'geometria', 'dificil',
 'Um círculo tem raio de 5 cm. Qual é a sua área aproximada? (Use π ≈ 3,14)',
 '78,5 cm²', '31,4 cm²', '15,7 cm²', '157 cm²',
 'a',
 'Área do círculo = π × r². Eleve o raio ao quadrado e multiplique por π.',
 'Área = 3,14 × 5² = 3,14 × 25 = 78,5 cm².'),

-- Q26
('matematica', 'porcentagem', 'dificil',
 'Um produto que custava R$ 200,00 teve um aumento de 15% e depois um desconto de 10%. Qual é o preço final?',
 'R$ 207,00', 'R$ 210,00', 'R$ 205,00', 'R$ 200,00',
 'a',
 'Aplique os percentuais em sequência: primeiro o aumento sobre R$ 200,00, depois o desconto sobre o novo valor.',
 'Aumento de 15%: 200 × 1,15 = R$ 230,00. Desconto de 10%: 230 × 0,90 = R$ 207,00.'),

-- Q27
('matematica', 'problemas', 'dificil',
 'Uma torneira enche um tanque em 4 horas e outra em 6 horas. Trabalhando juntas, em quantas horas o tanque estará cheio?',
 '5 horas', '2 horas', '2,4 horas', '3 horas',
 'c',
 'Some as frações do tanque preenchidas por hora: 1/4 + 1/6. O inverso da soma é o tempo total.',
 '1/4 + 1/6 = 3/12 + 2/12 = 5/12 do tanque por hora. Tempo = 1 ÷ (5/12) = 12/5 = 2,4 horas.'),

-- Q28
('matematica', 'potenciacao', 'dificil',
 'Qual é o resultado de 3³ + 2⁴?',
 '27', '35', '43', '55',
 'c',
 'Calcule cada potência separadamente e some os resultados.',
 '3³ = 3 × 3 × 3 = 27; 2⁴ = 2 × 2 × 2 × 2 = 16. Soma: 27 + 16 = 43.'),

-- Q29
('matematica', 'problemas', 'dificil',
 'Em uma turma de 40 alunos, 60% são meninas. Quantos meninos há na turma?',
 '24', '16', '20', '14',
 'b',
 '60% são meninas. Os meninos representam os 40% restantes.',
 '40% de 40 = 40 × 0,40 = 16 meninos. Ou: 60% de 40 = 24 meninas; 40 − 24 = 16 meninos.'),

-- Q30
('matematica', 'equacoes', 'dificil',
 'Qual é o valor de x em: 5(x − 2) = 3(x + 4)?',
 'x = 9', 'x = 10', 'x = 11', 'x = 7',
 'c',
 'Expanda os parênteses, depois agrupe os termos com x de um lado e os números do outro.',
 '5x − 10 = 3x + 12 → 5x − 3x = 12 + 10 → 2x = 22 → x = 11.');

-- ============================================================
--  QUESTÕES DE PORTUGUÊS
-- ============================================================

-- -------------------------------------------------------
--  PORTUGUÊS — FÁCIL (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q31
('portugues', 'ortografia', 'facil',
 'Qual palavra está escrita corretamente?',
 'Excessão', 'Exceção', 'Eseção', 'Exessão',
 'b',
 'Pense na família de palavras: exceto, excluir, excepcional...',
 'A forma correta é EXCEÇÃO, derivada do latim "exceptio". Palavras da mesma família: excepcional, exceto. Não se usa "ss" nem "ss" nessa palavra.'),

-- Q32
('portugues', 'ortografia', 'facil',
 'Assinale a alternativa com a grafia correta:',
 'Bicicreta', 'Biscicleta', 'Bicicleta', 'Bicicrêta',
 'c',
 'Esta palavra vem do inglês "bicycle" e do latim "bi" (dois) + "cyclus" (roda).',
 'A grafia correta é BICICLETA: bi (dois) + ci + cle + ta. Não há "r" nem acento nessa palavra.'),

-- Q33
('portugues', 'acentuacao', 'facil',
 'Qual das palavras abaixo é oxítona (acento na última sílaba)?',
 'Árvore', 'Café', 'Fácil', 'Número',
 'b',
 'Identifique onde está o acento tônico (a sílaba pronunciada com mais força) em cada palavra.',
 'CAFÉ: a sílaba tônica é "fé" (última). Árvore e Número são proparoxítonas; Fácil é paroxítona.'),

-- Q34
('portugues', 'acentuacao', 'facil',
 'Por que a palavra "pé" recebe acento gráfico?',
 'Por ser paroxítona terminada em vogal', 'Por ser oxítona terminada em "e"', 'Por ser esdrúxula', 'Por ser monossílaba átona',
 'b',
 'As regras de acentuação levam em conta a posição do acento tônico e a terminação da palavra.',
 '"Pé" é uma monossílaba tônica terminada em "e". Monossílabas tônicas terminadas em a(s), e(s) ou o(s) recebem acento gráfico.'),

-- Q35
('portugues', 'sinonimos', 'facil',
 'Qual é o sinônimo de "belo"?',
 'Feio', 'Bonito', 'Triste', 'Rápido',
 'b',
 'Sinônimos são palavras com significado parecido ou igual.',
 '"Belo" e "bonito" são sinônimos: ambos indicam algo agradável de se ver, com aparência atraente. "Feio" é antônimo de "belo".'),

-- Q36
('portugues', 'antonimos', 'facil',
 'Qual é o antônimo de "generoso"?',
 'Bondoso', 'Solidário', 'Mesquinho', 'Amável',
 'c',
 'Antônimos são palavras com significados opostos.',
 '"Mesquinho" é o antônimo de "generoso". Generoso é quem dá com abundância; mesquinho é quem evita dar ou compartilhar.'),

-- Q37
('portugues', 'sinonimos', 'facil',
 'Qual palavra tem o mesmo significado de "veloz"?',
 'Lento', 'Pesado', 'Rápido', 'Calmo',
 'c',
 'Pense em como descrevemos algo que se move com muita rapidez.',
 '"Veloz" e "rápido" são sinônimos: ambos descrevem algo que se move ou age com grande velocidade.'),

-- Q38
('portugues', 'ortografia', 'facil',
 'Qual é a forma correta de escrever o plural de "cidadão"?',
 'Cidadões', 'Cidadãos', 'Cidadãoes', 'Cidadãis',
 'b',
 'Palavras terminadas em "-ão" podem ter plural em "-ões", "-ães" ou "-ãos". Pense nas exceções mais comuns.',
 'O plural de CIDADÃO é CIDADÃOS (assim como mão → mãos, grão → grãos). Já "coração" → "corações" e "pão" → "pães" seguem regras diferentes.'),

-- Q39
('portugues', 'ortografia', 'facil',
 'Em qual alternativa o uso de "mal" ou "mau" está correto?',
 'O tempo está mau hoje.', 'Ele agiu de mau.', 'Ela está se sentindo mal.', 'O aluno foi mau aprovado.',
 'c',
 '"Mal" é advérbio (modifica verbo) ou substantivo; "mau" é adjetivo (qualifica substantivo).',
 '"Mal" modifica o verbo "sentindo" (advérbio), por isso "sentindo mal" é o correto. "Mau" seria adjetivo: "um mau aluno", "um mau dia".'),

-- Q40
('portugues', 'acentuacao', 'facil',
 'Qual palavra não precisa de acento gráfico segundo as regras do Acordo Ortográfico de 2009?',
 'Férias', 'Pêlo (substantivo)', 'Sábado', 'Lápis',
 'b',
 'O Acordo Ortográfico de 2009 eliminou o acento diferencial em algumas palavras que antes eram acentuadas para diferenciá-las.',
 'Após o Acordo de 2009, "pelo" (substantivo = cabelo) não recebe mais acento para diferenciá-lo da preposição "pelo". Férias, Sábado e Lápis continuam acentuados por suas regras próprias.');

-- -------------------------------------------------------
--  PORTUGUÊS — MÉDIO (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q41
('portugues', 'concordancia', 'medio',
 'Assinale a frase com concordância verbal correta:',
 'Faz dois anos que nos conhecemos.', 'Fazem dois anos que nos conhecemos.', 'Faz dois anos que se conhecemos.', 'Fazemos dois anos que nos conhecemos.',
 'a',
 'Quando "faz" indica tempo decorrido, ele é impessoal e fica sempre no singular.',
 '"Fazer" no sentido de tempo decorrido é um verbo impessoal: não tem sujeito, por isso fica sempre na 3ª pessoa do singular. Correto: "Faz dois anos..."'),

-- Q42
('portugues', 'concordancia', 'medio',
 'Qual frase apresenta concordância nominal correta?',
 'As crianças ficou felizes.', 'As crianças ficaram feliz.', 'As crianças ficaram felizes.', 'A criança ficaram felizes.',
 'c',
 'O adjetivo deve concordar em gênero e número com o substantivo a que se refere.',
 '"Crianças" é feminino plural; "felizes" é o plural de "feliz". Sujeito e predicativo concordam: As crianças ficaram felizes.'),

-- Q43
('portugues', 'figuras_linguagem', 'medio',
 '"Minha vida é um mar de problemas." Essa frase é um exemplo de:',
 'Comparação', 'Metáfora', 'Hipérbole', 'Ironia',
 'b',
 'Observe se a comparação usa "como" ou "que nem", ou se é feita diretamente sem esse elemento.',
 'É uma METÁFORA: a comparação é feita de forma direta, sem o uso de "como" ou "que nem". Se fosse "Minha vida é como um mar...", seria comparação (símile).'),

-- Q44
('portugues', 'figuras_linguagem', 'medio',
 '"Ela é tão rápida quanto uma lebre." Essa frase é um exemplo de:',
 'Metáfora', 'Hipérbole', 'Comparação', 'Personificação',
 'c',
 'Identifique se há uma palavra que introduz a comparação, como "como", "quanto", "tal qual".',
 'É uma COMPARAÇÃO (ou símile): o termo "quanto" introduz explicitamente o paralelo entre "ela" e "uma lebre". Na metáfora, esse conectivo não existe.'),

-- Q45
('portugues', 'interpretacao', 'medio',
 'Leia: "O livro estava sobre a mesa; ninguém o havia tocado em semanas." Que ideia o texto transmite sobre o livro?',
 'O livro era muito pesado.', 'O livro estava sendo muito lido.', 'O livro estava sendo ignorado ou esquecido.', 'O livro era novo e ainda não tinha sido aberto.',
 'c',
 'Reflita sobre o que significa "ninguém o havia tocado em semanas" — o que isso diz sobre a relação das pessoas com o livro?',
 'A expressão "ninguém o havia tocado em semanas" indica que o livro estava sendo esquecido ou ignorado. A localização "sobre a mesa" sugere que ele estava à vista, mas mesmo assim ninguém o usava.'),

-- Q46
('portugues', 'interpretacao', 'medio',
 'Leia: "Mesmo cansado, o professor explicou a matéria com paciência e dedicação." Qual característica do professor é destacada?',
 'Impaciência', 'Preguiça', 'Comprometimento', 'Indiferença',
 'c',
 'Observe a conjunção "mesmo" e o que ela indica sobre o comportamento do professor apesar do cansaço.',
 'A conjunção "mesmo" indica concessão: apesar do cansaço, o professor manteve paciência e dedicação. Isso revela seu comprometimento com os alunos.'),

-- Q47
('portugues', 'sinonimos', 'medio',
 'Qual é o sinônimo mais adequado para "árduo" no contexto "Foi um trabalho árduo"?',
 'Fácil', 'Agradável', 'Penoso', 'Rápido',
 'c',
 '"Árduo" vem do latim "arduus" e descreve algo que exige muito esforço.',
 '"Árduo" significa difícil, trabalhoso, penoso. Portanto, "penoso" é o sinônimo mais adequado. "Fácil" e "agradável" seriam antônimos.'),

-- Q48
('portugues', 'concordancia', 'medio',
 'Assinale a frase correta:',
 'Existem muita gente esperando.', 'Existe muitas pessoas esperando.', 'Existe muita gente esperando.', 'Existem muitas gentes esperando.',
 'c',
 '"Gente" é um substantivo coletivo no singular. O verbo deve concordar com o sujeito.',
 '"Gente" é singular; logo o verbo "existir" vai para o singular: "Existe muita gente esperando." Se o sujeito fosse "pessoas" (plural), seria "Existem muitas pessoas esperando."'),

-- Q49
('portugues', 'antonimos', 'medio',
 'Qual é o antônimo de "efêmero"?',
 'Rápido', 'Passageiro', 'Duradouro', 'Instantâneo',
 'c',
 '"Efêmero" descreve algo que dura muito pouco tempo. O antônimo seria...?',
 '"Efêmero" significa passageiro, de curta duração. Seu antônimo é "duradouro" (ou perene, eterno), que indica algo que dura muito ou para sempre.'),

-- Q50
('portugues', 'figuras_linguagem', 'medio',
 '"O vento sussurrou entre as árvores." Essa frase é um exemplo de:',
 'Hipérbole', 'Ironia', 'Personificação', 'Metáfora',
 'c',
 'Observe se foi atribuída uma característica humana a um ser não humano.',
 'É uma PERSONIFICAÇÃO (ou prosopopeia): o vento recebe uma ação humana ("sussurrou"). Atribuir ações ou sentimentos humanos a seres inanimados ou animais é personificação.');

-- -------------------------------------------------------
--  PORTUGUÊS — DIFÍCIL (10 questões)
-- -------------------------------------------------------

INSERT INTO `questions`
    (`jogo`, `categoria`, `dificuldade`, `pergunta`,
     `alternativa_a`, `alternativa_b`, `alternativa_c`, `alternativa_d`,
     `resposta_correta`, `dica`, `explicacao`)
VALUES

-- Q51
('portugues', 'figuras_linguagem', 'dificil',
 '"Já disse isso um milhão de vezes!" Essa frase é um exemplo de:',
 'Metáfora', 'Hipérbole', 'Ironia', 'Metonímia',
 'b',
 'A figura de linguagem que usa exagero intencional para dar ênfase é...',
 'É uma HIPÉRBOLE: o exagero intencional ("um milhão de vezes") é usado para enfatizar a ideia de que algo foi dito muitas vezes. Não é literal.'),

-- Q52
('portugues', 'figuras_linguagem', 'dificil',
 '"Que bonita prova! Errei todas as questões." Que figura de linguagem está presente?',
 'Hipérbole', 'Comparação', 'Ironia', 'Metáfora',
 'c',
 'Quando o significado oposto ao que está sendo dito é o real intuito do falante, temos...',
 'É IRONIA: o falante diz "bonita prova" com intenção oposta — a prova foi ruim, já que ele errou tudo. A ironia consiste em afirmar o contrário do que se quer dizer, geralmente com tom de crítica ou humor.'),

-- Q53
('portugues', 'figuras_linguagem', 'dificil',
 '"O Brasil ganhou o ouro na Copa." Nessa frase, "Brasil" está sendo usado no lugar de "seleção brasileira de futebol". Que figura de linguagem é essa?',
 'Hipérbole', 'Personificação', 'Comparação', 'Metonímia',
 'd',
 'Essa figura ocorre quando substituímos uma palavra por outra que tem relação lógica ou de proximidade com ela (parte pelo todo, causa pelo efeito, lugar pelo produto etc.).',
 'É METONÍMIA: usa-se "Brasil" (lugar/país) no lugar de "seleção brasileira de futebol" (o que o lugar representa). A metonímia é diferente da metáfora porque a relação entre os termos é real e não apenas de semelhança.'),

-- Q54
('portugues', 'interpretacao', 'dificil',
 'Leia: "Embora a tecnologia tenha facilitado a comunicação, muitos sociólogos alertam que ela também aprofundou o isolamento social." A palavra "embora" indica qual relação entre as orações?',
 'Causa', 'Consequência', 'Concessão', 'Adição',
 'c',
 'Identifique o tipo de conjunção: "embora" introduz uma ideia que parece contradizer a oração principal.',
 '"Embora" é uma conjunção concessiva: introduz uma ideia que contraria a expectativa da oração principal, mas não a impede. Estrutura: "Embora [fato contrário], [resultado]." Equivale a "apesar de que".'),

-- Q55
('portugues', 'interpretacao', 'dificil',
 'Leia: "A leitura é o passaporte para mundos que jamais poderíamos visitar de outra forma." Qual recurso linguístico o autor usa e qual é o seu efeito de sentido?',
 'Ironia, criando um efeito de crítica à leitura.', 'Hipérbole, exagerando os benefícios da leitura de forma literal.', 'Metáfora, comparando a leitura a um passaporte para ressaltar sua capacidade de transportar o leitor para outros universos.', 'Metonímia, substituindo o livro pelo passaporte.',
 'c',
 'Observe se há uma comparação direta (sem "como") que atribui uma qualidade de um objeto a outro.',
 'É uma METÁFORA: "passaporte" representa o poder da leitura de dar acesso a outros mundos. O efeito de sentido é valorizar a leitura como instrumento de expansão cultural e imaginativa.'),

-- Q56
('portugues', 'concordancia', 'dificil',
 'Assinale a frase com concordância verbal correta:',
 'Mais de um aluno faltaram hoje.', 'Mais de um aluno faltou hoje.', 'Mais de um alunos faltou hoje.', 'Mais de um alunos faltaram hoje.',
 'b',
 '"Mais de um" é uma expressão que, por indicar proximidade ao singular, geralmente leva o verbo ao singular.',
 'A expressão "mais de um" sugere proximidade ao singular e pede o verbo no singular: "Mais de um aluno faltou hoje." Exceção: se a oração indicar reciprocidade ("Mais de um se abraçaram"), pode-se usar o plural.'),

-- Q57
('portugues', 'concordancia', 'dificil',
 'Em "Comprei meia dúzia de ovos", o numeral "meia" concorda com:',
 'Ovos (masculino plural)', 'Dúzia (feminino singular)', 'A frase está errada; o correto é "meio dúzia"', 'Comprei (verbo)',
 'b',
 'O numeral ou adjetivo deve concordar com o substantivo mais próximo a que se refere diretamente.',
 '"Meia" concorda com "dúzia" (feminino singular), não com "ovos". Por isso dizemos "meia dúzia" e não "meio dúzia". O substantivo-núcleo aqui é "dúzia".'),

-- Q58
('portugues', 'variacao_linguistica', 'dificil',
 'Sobre variação linguística, assinale a afirmação CORRETA:',
 'A língua falada nas favelas é errada e deve ser corrigida na escola.', 'Existe uma única forma correta de falar o português, definida pela gramática normativa.', 'A variação linguística é natural e todas as variedades têm valor e lógica próprios, embora a norma culta seja exigida em contextos formais.', 'Sotaques regionais são erros que devem ser eliminados.',
 'c',
 'Linguistas modernos distinguem "certo/errado" de "adequado/inadequado" ao contexto de uso.',
 'A linguística moderna reconhece que toda variedade linguística é sistemática e válida. O conceito de "erro" linguístico está ligado à adequação ao contexto: a norma culta é exigida em situações formais, mas dialetos regionais e sociais não são "errados", apenas diferentes.'),

-- Q59
('portugues', 'coesao_coerencia', 'dificil',
 'Leia: "Pedro estudou muito para a prova. _____, tirou nota baixa." Qual conectivo preenche corretamente a lacuna, indicando um resultado inesperado?',
 'Portanto', 'Por isso', 'No entanto', 'Além disso',
 'c',
 'O resultado (nota baixa) é contrário ao esperado após tanto estudo. Que tipo de conector expressa essa ideia?',
 '"No entanto" é um conectivo adversativo: indica oposição ou contraste entre as ideias. O fato de tirar nota baixa contraria a expectativa gerada por "estudou muito". "Portanto" e "por isso" indicariam consequência, não contraste.'),

-- Q60
('portugues', 'coesao_coerencia', 'dificil',
 'Qual alternativa apresenta um texto com INCOERÊNCIA?',
 '"Chovia muito. Por isso, levei guarda-chuva."', '"Estava com fome. Então, fui jantar."', '"Ela estava feliz. Consequentemente, estava chorando de tristeza."', '"Estudei bastante. Logo, estava confiante na prova."',
 'c',
 'Incoerência ocorre quando as ideias se contradizem ou não fazem sentido juntas.',
 'A alternativa C é incoerente: "estar feliz" e "chorar de tristeza" são estados contraditórios ligados por "consequentemente", o que não faz sentido lógico. A coerência exige que as ideias se complementem ou se encadeiem de forma lógica.');

-- ============================================================
--  USUÁRIO DEMO / TESTE INICIAL
-- ============================================================
-- Email: aluno.teste@studiogameover.com | Senha: 12345678

INSERT INTO `users` (`nome`, `username`, `email`, `senha`, `serie`, `xp`, `nivel`, `pontuacao`, `avatar`)
VALUES ('Aluno Teste', 'aluno_teste', 'aluno.teste@studiogameover.com', '$2y$10$8R83QzN1r1K.6gA6Mv8x1u2P0L8y7W6V5U4T3S2R1Q0P9O8N7M6L5', '7°', 150, 1, 150, 'avatar1')
ON DUPLICATE KEY UPDATE `id`=`id`;
