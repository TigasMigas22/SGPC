# ⛽ Sistema de Gestão de Postos de Combustível
Um sistema de gestão desenvolvido para postos de combustível, permitindo administrar utilizadores, vendas, serviços, fidelização de clientes e agendamentos.  
O sistema tem um administrador único e define diferentes tipos de utilizadores, cada um com permissões específicas.  

---

## 📖 Visão Geral
Este projeto implementa um sistema multi-utilizador com autenticação e perfis diferenciados.  
Cada perfil possui funcionalidades próprias que abrangem desde gestão administrativa até operações de cliente.  

Funcionalidades principais:
- Gestão de utilizadores e autenticação  
- Registo de vendas e inserção de produtos  
- Agendamento e atribuição de serviços  
- Fidelização e resgate de prémios  
- Alertas operacionais para bombas  
- Encerramento e autoexclusão de contas  

---

## 🎯 Objetivos de Aprendizagem
- Consolidar conhecimentos em desenvolvimento web com **PHP, HTML, CSS e JavaScript**  
- Integrar **bases de dados MySQL** com interface em PHP  
- Aplicar **frameworks e bibliotecas** (Bootstrap)  
- Usar **XAMPP** como ambiente de servidor local  
- Organizar perfis de utilizadores com permissões distintas  

---

## 📊 Base de Dados
O sistema utiliza **MySQL Workbench** para gerir e modelar a base de dados.  
Tabelas principais incluem:
- `utilizadores`  
- `servico`  
- `produto`  
- `venda` (total da venda) 
- `venda_item` (individualização do produto)
- `login`
- `movimento_cartao`
- `indisponibilidade`    
- `cartao_fidelidade`  
- `bomba`
- `agendamento`
- `alerta`      

---

## 🛠️ Stack Tecnológica
- **Frontend**: HTML, CSS, JavaScript, Bootstrap  
- **Backend**: PHP 
- **Base de Dados**: MySQL  
- **Servidor Local**: XAMPP (Apache + MySQL)  
- **IDE**: Visual Studio Code  

---

## 🚀 Instruções de Instalação

### Pré-requisitos
- [XAMPP](https://www.apachefriends.org/index.html) instalado  
- MySQL Workbench instalado  

### Passos
1. Colocar os ficheiros do projeto na pasta `htdocs` do XAMPP.  
2. Criar a base de dados no **MySQL Workbench** e importar o ficheiro SQL.  
3. Iniciar **Apache** e **MySQL** no painel do XAMPP.  
4. Aceder via navegador:  


---

## 📁 Estrutura do Projeto
SistemaGestaoPosto/
│
├── html/ # Páginas e formulários HTML
├── php/ # Código backend PHP
│ ├── autenticacao/ # Login, logout, gestão de sessão
│ ├── agendamento/ # Serviços e agendamentos
│ ├── vendas/ # Registo de vendas
│ ├── alertas/ # Gestão de alertas
│ └── fidelizacao/ # Fidelização de clientes
├── css/ # Folhas de estilo
├── js/ # Scripts JavaScript
├── bd/ # Scripts SQL
└── README.md # Documentação


---

## 👥 Perfis de Utilizador e Funcionalidades

### 👤 Administrador
- Gerir utilizadores  
- Registar novos utilizadores  
- Editar dados pessoais  
- Autoexcluir conta  

### 🏪 Gerente de Posto
- Ver alertas de bombas  
- Editar dados pessoais  

### ⛽ Operador
- Registar vendas  
- Inserir produtos  
- Consultar histórico de pontos  
- Editar dados pessoais  

### 📋 Funcionário Administrativo
- Inserir serviços  
- Atribuir funcionário a serviços  
- Gerir fidelização de clientes  
- Editar dados pessoais  

### 🔧 Funcionário de Serviços
- Ver agendamentos  
- Executar comandos LSS  
- Editar dados pessoais  

### 🧑‍💼 Cliente
- Agendar serviços  
- Consultar catálogo de prémios  
- Consultar histórico de pontos  
- Resgatar prémios  
- Ver agendamentos  
- Editar dados pessoais  
- Encerrar conta  

---

## 📌 Como Executar
1. Iniciar Apache e MySQL no painel do XAMPP.  
2. Garantir que a base de dados está criada e populada.  
3. Aceder pelo navegador ao endereço configurado (ex.: `http://localhost/SistemaGestaoPosto`).  

---
