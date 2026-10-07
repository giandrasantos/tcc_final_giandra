# 🎮 STUDIO GAME OVER — MathPlay Solutions

Plataforma Web Educacional Gamificada desenvolvida para a empresa **Studio Game Over**, apresentada como Trabalho de Conclusão de Curso (TCC).

---

## 🎓 Sobre a Plataforma

**MathPlay Solutions** é uma plataforma educacional interativa voltada para estudantes do Ensino Fundamental II (6º ao 9º ano). A proposta é transformar a aprendizagem em uma aventura gamificada através de 2 jogos principais com dicas pedagógicas e explicações em caso de erros:

1. 🧮 **Desafio Matemático** (Números Inteiros, Frações, Porcentagem, Equações de 1º grau, Geometria, Potenciação e Radiciação)
2. 📚 **Desafio das Palavras** (Ortografia, Acentuação, Sinônimos/Antônimos, Concordância, Figuras de Linguagem e Interpretação Textual)

---

## 🛠️ Tecnologias Utilizadas

- **Backend:** PHP 8.x + Sessions + PDO (Prepared Statements com suporte a SQL Injection guard)
- **Frontend:** HTML5 Semântico + CSS3 (Design System com Variáveis, CSS Grid, Flexbox, Glassmorphism, Microinterações) + JavaScript Vanilla (ES6+)
- **Banco de Dados:** MySQL / MariaDB (compatível com XAMPP e Laragon)
- **Ícones:** Lucide Icons (CDN SVG)
- **Tipografia:** Orbitron & Inter (Google Fonts)

---

## 📁 Estrutura do Projeto

```
studio-game-over/
│
├── index.php                  ← Landing Page pública oficial
├── login.php                  ← Tela de Login de Alunos
├── cadastro.php               ← Tela de Cadastro com seleção de Avatar Gamer
├── dashboard.php              ← Painel Principal do Estudante
├── perfil.php                 ← Perfil & Troca de Avatar
├── ranking.php                ← Ranking Global e por Matéria
├── historico.php              ← Histórico Completo de Partidas
├── logout.php                 ← Encerrar Sessão
│
├── jogos/
│   ├── matematica.php         ← Interface do Desafio Matemático
│   └── portugues.php          ← Interface do Desafio das Palavras
│
├── api/
│   ├── login.php              ← Endpoint de Autenticação
│   ├── cadastro.php           ← Endpoint de Cadastro de Alunos
│   ├── logout.php             ← Endpoint de Logout
│   ├── get-questions.php      ← Busca de Questões Sanitizadas (Segurança de Gabarito no Servidor)
│   ├── verify-answer.php      ← Validação de Resposta, Dica e Explicação
│   ├── salvar-partida.php     ← Registro de Partida, XP e Recálculo de Nível
│   ├── conquistas.php         ← Verificação e Liberação de Medalhas/Conquistas
│   ├── update-avatar.php      ← Atualização de Avatar
│   └── ranking.php            ← Dados do Ranking via AJAX
│
├── config/
│   └── database.php           ← Conexão Singleton PDO MySQL
│
├── includes/
│   ├── header.php             ← Navegação Reutilizável com Dados de Sessão
│   ├── footer.php             ← Footer Oficial Studio Game Over
│   └── auth.php               ← Middleware de Proteção de Autenticação
│
├── assets/
│   ├── css/
│   │   ├── style.css          ← Design System Base & Variáveis CSS
│   │   ├── login.css          ← Estilos da Tela de Autenticação
│   │   ├── dashboard.css      ← Estilos do Painel do Estudante
│   │   ├── jogos.css          ← Estilos dos Jogos e Telas de Resultado
│   │   └── responsivo.css     ← Media Queries (Mobile-First)
│   │
│   └── js/
│       ├── main.js            ← Utilitários Globais (Toasts, Mobile Nav)
│       ├── dashboard.js       ← Animações de Stats e Barra de XP
│       ├── matematica.js      ← Lógica do Jogo de Matemática
│       └── portugues.js       ← Lógica do Jogo de Português
│
├── database/
│   └── database.sql           ← Schema MySQL + Seed com 60 Questões + Conquistas
│
└── README.md                  ← Documentação do Projeto
```

---

## 🚀 Como Executar o Projeto via XAMPP

1. **Copie a pasta do projeto:**
   Copie a pasta `studio-game-over` para o diretório `htdocs` do seu XAMPP (exemplo: `C:\xampp\htdocs\studio-game-over`).

2. **Inicie os serviços no XAMPP Control Panel:**
   - Inicie o **Apache**
   - Inicie o **MySQL**

3. **Importe o Banco de Dados:**
   - Abra o navegador e acesse `http://localhost/phpmyadmin`
   - Clique na aba **Importar** (ou crie um banco de dados com nome `mathplay`)
   - Selecione o arquivo `database/database.sql` contido no projeto
   - Clique em **Executar** para criar as tabelas e inserir as 60 questões iniciais e conquistas.

4. **Acesse a Aplicação:**
   - Abra o navegador no endereço: `http://localhost/studio-game-over`

---

## 🛡️ Segurança & Funcionalidades

- **Criptografia de Senhas:** Senhas armazenadas via `password_hash(..., PASSWORD_BCRYPT)` e autenticadas via `password_verify()`.
- **Prevenção SQL Injection:** Todas as consultas utilizam Prepared Statements do PDO.
- **Proteção do Gabarito:** As alternativas corretas das perguntas não trafegam no payload inicial para o navegador; a verificação da resposta ocorre via token de sessão em endpoint autenticado (`verify-answer.php`).
- **Sistema Pedagógico de Dicas:** Quando o estudante erra uma questão, a resposta correta não é entregue de imediato; em vez disso, exibe-se uma dica estimulante e a explicação lógica da questão para transformar o erro em aprendizado.

---

## 🏆 Créditos
Projeto desenvolvido pela **Studio Game Over** para o produto **MathPlay Solutions**.
© 2025 Studio Game Over. Todos os direitos reservados.
