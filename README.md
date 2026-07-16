# 💇 Sistema de Agendamento para Salão de Beleza

Sistema web desenvolvido para gerenciamento de salões de beleza, permitindo que clientes realizem agendamentos online e que administradores gerenciem serviços, profissionais e horários.

O projeto é dividido em **Frontend** e **Backend**, seguindo uma arquitetura desacoplada, comunicando-se através de uma API REST.

---

# ✨ Funcionalidades

## Área do Cliente

- Visualização dos serviços disponíveis
- Escolha do profissional
- Consulta de horários disponíveis
- Agendamento online
- Login utilizando conta Google
- Validação de horários indisponíveis
- Interface responsiva

---

## Área Administrativa

- Login administrativo
- Cadastro de categorias
- Cadastro de serviços
- Cadastro de profissionais
- Definição da duração dos serviços
- Cadastro de horários de atendimento
- Bloqueio de horários
- Visualização dos agendamentos
- Aprovação ou cancelamento de agendamentos

---

# 🏗️ Arquitetura

O projeto é composto por três serviços executados via Docker.

```
Frontend (Next.js)
        │
        ▼
Backend (PHP API)
        │
        ▼
MariaDB
```

Todo o acesso aos dados é realizado através da API, mantendo o frontend totalmente desacoplado do banco de dados.

---

# 🚀 Tecnologias Utilizadas

## Frontend

- Next.js
- React
- TypeScript
- Tailwind CSS
- NextAuth
- Google OAuth

---

## Backend

- PHP 8
- PDO
- API REST
- JSON

---

## Banco de Dados

- MariaDB

---

## Infraestrutura

- Docker
- Docker Compose

---

## Controle de Versão

- Git
- GitHub

---

# 📂 Estrutura do Projeto

```
salao/
│
├── frontend/
│   ├── app/
│   ├── components/
│   ├── public/
│   ├── styles/
│   └── ...
│
├── backend/
│   ├── api/
│   ├── config/
│   ├── models/
│   ├── uploads/
│   └── ...
│
├── docker-compose.yml
└── README.md
```

---

# 🗄️ Banco de Dados

O sistema utiliza as seguintes entidades principais:

- Salões
- Clientes
- Categorias
- Serviços
- Profissionais
- Horários
- Bloqueios
- Agendamentos

Essas tabelas permitem controlar toda a disponibilidade dos profissionais e impedir conflitos de horário durante novos agendamentos.

---

# 🔐 Autenticação

O sistema possui dois tipos de autenticação:

### Clientes

- Login com conta Google utilizando NextAuth.

### Administradores

- Login com e-mail e senha.
- Senhas armazenadas utilizando hash (`password_hash` do PHP).

---

# 📅 Fluxo de Agendamento

1. O cliente acessa a página do salão.
2. Escolhe um serviço.
3. Seleciona um profissional.
4. Visualiza os horários disponíveis.
5. Realiza login com Google.
6. Confirma o agendamento.
7. O agendamento é registrado como **Pendente**.
8. O administrador pode aprovar ou cancelar o agendamento.

---

# 📌 Regras de Negócio

- Não permite dois agendamentos para o mesmo profissional no mesmo horário.
- Considera a duração do serviço para cálculo da disponibilidade.
- Horários bloqueados não aparecem para o cliente.
- Apenas administradores podem gerenciar cadastros e aprovar agendamentos.
- Clientes autenticados podem realizar novos agendamentos.

---

# 🎯 Objetivo do Projeto

O objetivo do sistema é facilitar o gerenciamento de salões de beleza, automatizando o processo de agendamento e reduzindo conflitos de horários, proporcionando uma melhor experiência tanto para clientes quanto para administradores.

---

# 📈 Possíveis Melhorias Futuras

- Área "Meus Agendamentos"
- Reagendamento de horários
- Cancelamento pelo cliente
- Notificações por e-mail
- Integração com WhatsApp
- Painel administrativo com indicadores
- Dashboard com gráficos
- Pagamento online
- Cadastro de múltiplas unidades
- Sistema de avaliações

---

# ▶️ Como Executar

Clone o repositório:

```bash
git clone https://github.com/estrmrnd/salao.git
```

Entre na pasta:

```bash
cd salao
```

Suba os containers:

```bash
docker compose up --build
```

A aplicação estará disponível em:

- Frontend: http://localhost:3000
- Backend: http://localhost:8000

---

# 👩‍💻 Desenvolvido por

**Ester Miranda**

Projeto ainda em desenvolvimento para estudos de Full Stack utilizando Next.js, PHP, MariaDB e Docker.
