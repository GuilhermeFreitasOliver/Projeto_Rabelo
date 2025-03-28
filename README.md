# Sistema de Catálogo de Filmes

Este é um sistema completo de Catálogo de Filmes com front-end e back-end, desenvolvido utilizando PHP, MySQL, Bootstrap, HTML5 semântico e JavaScript/jQuery.

## Estrutura do Projeto

```
/
├── assets/
│   ├── css/
│   │   ├── reset.css
│   │   ├── layout.css
│   │   └── components.css
│   ├── js/
│   │   ├── validation.js
│   │   └── slideshow.js
│   └── images/
│       └── uploads/
├── includes/
│   ├── header.php
│   ├── footer.php
│   ├── config.php
│   └── db.php
├── admin/
│   ├── index.php
│   ├── filmes/
│   │   ├── create.php
│   │   ├── read.php
│   │   ├── update.php
│   │   └── delete.php
│   └── generos/
│       ├── create.php
│       ├── read.php
│       ├── update.php
│       └── delete.php
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
└── index.php
```

## Funcionalidades

- Front-end responsivo com Bootstrap
- HTML5 semântico
- Slideshow na página inicial
- Autenticação de usuários
- CRUD completo para filmes e gêneros
- Upload e redimensionamento de imagens
- Validação de formulários com JavaScript/jQuery

## Requisitos

- PHP 7.0 ou superior
- MySQL 5.7 ou superior
- Extensão GD do PHP habilitada para manipulação de imagens