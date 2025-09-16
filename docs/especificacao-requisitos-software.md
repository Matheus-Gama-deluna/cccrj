# Especificação de Requisitos de Software

## 1. Introdução

### 1.1 Finalidade
Este documento tem como objetivo especificar os requisitos funcionais e não funcionais do sistema do Centro de Comércio de Café do Rio de Janeiro (CCCRJ), servindo como base para o desenvolvimento e manutenção do sistema.

### 1.2 Escopo
O sistema abrange um portal web informativo sobre o mercado cafeeiro do Rio de Janeiro, incluindo funcionalidades de exibição de cotações, notícias, relatórios técnicos e uma área administrativa para gerenciamento de conteúdo.

### 1.3 Definições, Acrônimos e Abreviações
- CCCRJ: Centro de Comércio de Café do Rio de Janeiro
- MVP: Minimum Viable Product (Produto Mínimo Viável)
- FTP: File Transfer Protocol
- PDF: Portable Document Format
- API: Application Programming Interface

### 1.4 Referências
- Plano de Implementação do Projeto
- Análise do Sistema Atual

### 1.5 Visão Geral
Este documento está organizado da seguinte forma:
- Descrição Geral do Sistema
- Requisitos Funcionais
- Requisitos Não Funcionais
- Requisitos de Interface Externa

## 2. Descrição Geral

### 2.1 Perspectiva do Produto
O sistema é um portal web frontend-only que apresenta informações sobre o mercado cafeeiro do Rio de Janeiro, com funcionalidades de exibição de dados em tempo real, notícias e relatórios técnicos. A área administrativa permite o gerenciamento de conteúdo.

### 2.2 Funções do Produto
- Exibição de cotações de café em tempo real
- Apresentação de notícias do setor cafeeiro
- Disponibilização de relatórios técnicos em PDF
- Sistema de administração de conteúdo
- Interface responsiva para diferentes dispositivos

### 2.3 Características dos Usuários
- **Visitantes**: Usuários não autenticados que acessam as informações públicas
- **Administradores**: Usuários autenticados com permissão para gerenciar conteúdo

### 2.4 Restrições
- Arquitetura frontend separada do backend
- Frontend hospedado em serviço de hospedagem estática
- Backend desenvolvido com PHP e Laravel
- Navegadores modernos
- Conexão com internet para atualização de dados

### 2.5 Suposições e Dependências
- Disponibilidade de API para obtenção de dados em tempo real
- Servidor FTP para armazenamento de relatórios
- Sistema de autenticação seguro

## 3. Requisitos Funcionais

### 3.1 Módulo de Cotações
| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-001 | Exibir cotações de diferentes tipos de café (Arábica, Conilon, Especial) | Alta |
| RF-002 | Atualizar cotações automaticamente em intervalos regulares | Alta |
| RF-003 | Mostrar histórico de cotações | Média |
| RF-004 | Permitir filtragem por tipo de café | Baixa |

### 3.2 Módulo de Notícias
| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-005 | Exibir lista de notícias do setor cafeeiro | Alta |
| RF-006 | Permitir paginação de notícias | Alta |
| RF-007 | Filtrar notícias por categoria | Média |
| RF-008 | Buscar notícias por palavras-chave | Baixa |

### 3.3 Módulo de Relatórios
| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-009 | Exibir lista de relatórios técnicos em PDF | Alta |
| RF-010 | Permitir download de relatórios | Alta |
| RF-011 | Visualizar relatórios diretamente no navegador | Média |
| RF-012 | Buscar relatórios por data ou título | Baixa |

### 3.4 Sistema de Autenticação
| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-013 | Permitir login de administradores | Alta |
| RF-014 | Gerenciar sessão de usuário | Alta |
| RF-015 | Permitir logout seguro | Alta |

### 3.5 Área Administrativa
| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-016 | Dashboard com métricas do sistema | Alta |
| RF-017 | Interface para envio de relatórios em PDF | Alta |
| RF-018 | Gerenciamento de notícias | Média |
| RF-019 | Configuração de módulos ativos | Média |
| RF-020 | Gerenciamento de usuários administradores | Baixa |

## 4. Requisitos Não Funcionais

### 4.1 Requisitos de Desempenho
| ID | Descrição |
|----|-----------|
| RNF-001 | Tempo de carregamento da página principal inferior a 3 segundos |
| RNF-002 | Atualização de cotações com latência inferior a 1 segundo |
| RNF-003 | Suportar pelo menos 1000 usuários simultâneos |

### 4.2 Requisitos de Segurança
| ID | Descrição |
|----|-----------|
| RNF-004 | Autenticação segura de administradores |
| RNF-005 | Proteção contra ataques XSS e CSRF |
| RNF-006 | Criptografia de dados sensíveis |
| RNF-007 | Validação de entrada de dados |

### 4.3 Requisitos de Confiabilidade
| ID | Descrição |
|----|-----------|
| RNF-008 | Disponibilidade mínima de 99% |
| RNF-009 | Tempo médio para recuperação de falhas inferior a 30 minutos |
| RNF-010 | Backup diário dos dados |

### 4.4 Requisitos de Usabilidade
| ID | Descrição |
|----|-----------|
| RNF-011 | Interface responsiva para dispositivos móveis e desktop |
| RNF-012 | Conformidade com padrões WCAG 2.1 AA |
| RNF-013 | Tempo de aprendizado para usuários novos inferior a 15 minutos |

### 4.5 Requisitos de Manutenibilidade
| ID | Descrição |
|----|-----------|
| RNF-014 | Código modular com baixo acoplamento entre componentes |
| RNF-015 | Documentação técnica completa |
| RNF-016 | Cobertura de testes unitários superior a 80% |

### 4.6 Requisitos de Portabilidade
| ID | Descrição |
|----|-----------|
| RNF-017 | Compatibilidade com os principais navegadores modernos |
| RNF-018 | Funcionamento em diferentes sistemas operacionais (Windows, macOS, Linux) |

## 5. Requisitos de Interface Externa

### 5.1 Interfaces de Usuário
- Interface web responsiva acessada por navegadores modernos
- Área administrativa com dashboard e formulários de gerenciamento
- Páginas públicas com informações de cotações, notícias e relatórios

### 5.2 Interfaces de Hardware
- Compatibilidade com dispositivos desktop, tablets e smartphones
- Suporte a diferentes resoluções de tela

### 5.3 Interfaces de Software
- Integração com API Laravel para obtenção de cotações em tempo real
- Integração com servidor FTP para gerenciamento de relatórios
- Uso de bibliotecas JavaScript e CSS (Tailwind CSS, Material Icons)

### 5.4 Interfaces de Comunicação
- Protocolo HTTPS para comunicação segura
- Requisições HTTP/HTTPS para API Laravel
- Conexão FTP para upload/download de relatórios