# Mapa Cidadão API

## 📋 Descrição

**Mapa Cidadão API** é uma aplicação backend desenvolvida em Laravel que permite o registro e monitoramento de ocorrências urbanas em um mapa interativo. Com foco em participação cidadã e gestão urbana, a API possibilita que usuários registrem problemas como:

- 🗑️ Acúmulo de lixo
- 💡 Falta de iluminação pública
- 🛣️ Problemas de pavimentação
- 🌊 Alagamentos
- 🚧 Outros tipos de ocorrências

Cada ocorrência cadastrada contém:
- 📍 A localização geográfica precisa do problema (via coordenadas GPS)
- 📝 Uma descrição breve do que está acontecendo
- 🏷️ Um tipo pré-definido de ocorrência
- 👍 Sistema de likes para priorização

O objetivo principal do projeto é fornecer uma base de dados georreferenciada para que prefeituras, gestores públicos e cidadãos possam visualizar, acompanhar e priorizar ações de manutenção urbana com base em dados colaborativos.

A Mapa Cidadão API serve como backend de uma aplicação web e oferece uma interface RESTful para integração com frontends modernos.

## ✨ Funcionalidades

- **Registro de Ocorrências**: Usuários podem registrar ocorrências com localização geográfica.
- **Tipos de Ocorrência**: Sistema de categorização com enums para diferentes tipos de problemas urbanos.
- **Sistema de Likes**: Usuários podem dar likes em ocorrências para indicar prioridade.
- **Autenticação**: Integração com Laravel Sanctum para autenticação de usuários.
- **Geolocalização**: Utiliza PostGIS para armazenamento e consultas geoespaciais.
- **API RESTful**: Endpoints bem definidos para integração com frontends.
- **Documentação**: Documentação automática da API com Scribe.

## 🛠️ Tecnologias

<img align="left" alt="PHP" title="PHP" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/php/php-original.svg" />
<img align="left" alt="Laravel" title="Laravel" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/laravel/laravel-original.svg" />
<img align="left" alt="Docker" title="Docker" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original.svg" />
<img align="left" alt="PostgreSQL" title="PostgreSQL" width="30px" style="padding-right: 10px;" src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/postgresql/postgresql-original.svg" />

<br><br>

- **PHP 8.4**
- **Laravel 12**
- **Docker & Docker Compose**
- **PostgreSQL com PostGIS**
- **Laravel Sanctum** (para autenticação)
- **Scribe** (para documentação da API)
- **PHPUnit** (para testes)

## 📋 Pré-requisitos

Antes de começar, certifique-se de que você tem instalado em sua máquina:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

## 🚀 Instalação

### 1. Clonar o Repositório

```bash

cd mapa_cidadao_api
```

### 2. Configurar Containers Docker

Copie o arquivo de exemplo do Docker Compose e inicie os containers:

```bash
cp docker-compose.example.yml docker-compose.yml
docker compose up -d
```

### 3. Configurar Ambiente

Copie os arquivos de configuração do ambiente:

```bash
cp .env.example .env
cp .env.example.testing .env.testing
```

### 4. Entrar no Container da Aplicação

```bash
docker compose exec app bash
```

### 5. Configurar Chaves da Aplicação

Gere as chaves para os ambientes de produção e teste:

```bash
php artisan key:generate
php artisan key:generate --env=testing
```

### 6. Instalar Dependências

```bash
composer install
```

### 7. Executar Migrações e Seeds

```bash
php artisan migrate
php artisan db:seed
```

### 8. Executar Testes

```bash
composer test
```

A aplicação estará rodando em `http://localhost:8000/`.

## 📖 Uso

### Endpoints Principais

A API oferece os seguintes endpoints principais:

#### Autenticação
- `POST /api/register` - Registrar novo usuário
- `POST /api/login` - Fazer login
- `POST /api/logout` - Fazer logout

#### Ocorrências
- `GET /api/ocurrences` - Listar ocorrências
- `POST /api/ocurrences` - Criar nova ocorrência
- `GET /api/ocurrences/{id}` - Obter ocorrência específica
- `PUT /api/ocurrences/{id}` - Atualizar ocorrência
- `DELETE /api/ocurrences/{id}` - Deletar ocorrência
- `POST /api/ocurrences/{id}/like` - Dar like em ocorrência

#### Tipos de Ocorrência
- `GET /api/types-ocurrence` - Listar tipos de ocorrência

### Documentação da API

A documentação completa da API pode ser acessada em `http://localhost:8000/docs` (usando Scribe).

## 🧪 Testes

Para executar os testes:

```bash
composer test
```

Ou usando PHPUnit diretamente:

```bash
./vendor/bin/phpunit
```

## 🤝 Contribuição

Contribuições são bem-vindas! Para contribuir:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/nova-feature`)
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 📞 Contato

Para dúvidas ou sugestões, entre em contato com a equipe de desenvolvimento.

---

Desenvolvido com ❤️ usando Laravel