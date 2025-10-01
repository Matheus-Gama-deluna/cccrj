# Plano para Implementar Extração de Cotações de PDF Usando API de IA

## 1. Visão Geral

### Objetivo
Implementar um sistema robusto que utilize API de IA para extrair automaticamente cotações de café de arquivos PDF como o `boletim.pdf` e integrar essa funcionalidade ao sistema atual do CCCRJ.

### Justificativa
O sistema atual usa cotações simuladas que mudam aleatoriamente. Com a implementação da extração via IA, poderemos:
- Atualizar automaticamente as cotações com base em informações reais do PDF
- Melhorar a confiabilidade do sistema
- Automatizar o processo de atualização de informações

## 2. Tipos de Cotações a Serem Extraídas

Com base nas informações fornecidas, o sistema deve extrair:

### 2.1 GRUPO I (BASE VARGINHA/MG):
- Tipo C. Interno $600 \text{ DEF. } 11+$
- Tipo 6/7, BC Duro
- Tipo 6, BC Fino

### 2.2 GRUPO II (BASE VITÓRIA):
- Tipo C. Interno 600 DEF.
- Tipo 7, Bica
- Tipo 5/6 15/16 (Pronto Embarque)
- Tipo 2/3 17/18 (Pronto Embarque)

### 2.3 CONILLON (BASE VITÓRIA):
- Tipo 7 Bica Corrida
- Tipo 5/6 13 UP Pronto Embarque

## 3. Arquitetura do Sistema

### 3.1 Componentes Principais

1. **Frontend**
   - `assets/js/components/quotes.js` - Componente de visualização de cotações
   - `assets/js/admin.js` - Componente de upload com integração à nova API

2. **Backend**
   - `api/upload_report_openrouter.php` - Novo endpoint para processamento de PDF com API de IA
   - `api/get_quotes_openrouter.php` - Novo endpoint para obter cotações extraídas via IA
   - `api/config_openrouter.php` - Configurações para acesso à API do OpenRouter

3. **Serviços Externos**
   - OpenRouter API (com suporte a modelos como GPT-4 Vision, Claude ou outros modelos de análise de documentos)

### 3.2 Fluxo de Processamento

```
Admin faz upload do boletim.pdf → Backend recebe arquivo → Backend envia para OpenRouter API → 
API processa PDF → Backend recebe JSON com cotações → Backend salva em data/quotes_openrouter.json → 
Frontend atualiza cotações em tempo real
```

## 4. Implementação do Sistema

### 4.1 Configuração do Backend

#### 4.1.1 Criar arquivo de configuração
- `api/config_openrouter.php` - Configurações de acesso à API do OpenRouter
- Incluir chave de API, URL do endpoint e modelo de IA preferido

#### 4.1.2 Criar endpoint para processamento de PDF
- `api/upload_report_openrouter.php` - Endpoint para receber PDF e enviar para análise com IA
- Implementar lógica de envio para OpenRouter API
- Implementar tratamento de resposta e extração de dados

#### 4.1.3 Criar endpoint para obtenção de cotações
- `api/get_quotes_openrouter.php` - Endpoint para retornar cotações extraídas via IA
- Implementar mecanismo de fallback para cotações anteriores em caso de falha

### 4.2 Integração com o Frontend

#### 4.2.1 Atualizar componente de cotações
- Modificar `assets/js/components/quotes.js` para buscar cotações do novo endpoint
- Implementar mecanismo de fallback para cotações simuladas em caso de falha
- Atualizar interface para exibir os tipos específicos de café

#### 4.2.2 Atualizar componente administrativo
- Modificar `assets/js/admin.js` para usar o novo endpoint de upload com IA
- Adicionar feedback visual durante o processamento da IA

### 4.3 Estrutura de Dados

#### 4.3.1 Formato de resposta da API de IA
```
{
  "success": true,
  "data": {
    "date": "2024-10-01",
    "base": "Varginha/MG",
    "quotes": {
      "grupo_i": {
        "tipo_c_interno_600_def_11": {
          "name": "Tipo C. Interno $600 DEF. 11+$",
          "price": 1250.75,
          "unit": "R$/saca 60kg"
        },
        "tipo_6_7_bc_duro": {
          "name": "Tipo 6/7, BC Duro",
          "price": 1100.00,
          "unit": "R$/saca 60kg"
        },
        "tipo_6_bc_fino": {
          "name": "Tipo 6, BC Fino",
          "price": 1150.50,
          "unit": "R$/saca 60kg"
        }
      },
      "grupo_ii": {
        "tipo_c_interno_600_def": {
          "name": "Tipo C. Interno 600 DEF.",
          "price": 1200.00,
          "unit": "R$/saca 60kg"
        },
        "tipo_7_bica": {
          "name": "Tipo 7, Bica",
          "price": 1180.00,
          "unit": "R$/saca 60kg"
        },
        "tipo_5_6_15_16_pronto_embarque": {
          "name": "Tipo 5/6 15/16 (Pronto Embarque)",
          "price": 1160.00,
          "unit": "R$/saca 60kg"
        },
        "tipo_2_3_17_18_pronto_embarque": {
          "name": "Tipo 2/3 17/18 (Pronto Embarque)",
          "price": 1140.00,
          "unit": "R$/saca 60kg"
        }
      },
      "conillon": {
        "tipo_7_bica_corrida": {
          "name": "Tipo 7 Bica Corrida",
          "price": 980.50,
          "unit": "R$/saca 60kg"
        },
        "tipo_5_6_13_up_pronto_embarque": {
          "name": "Tipo 5/6 13 UP Pronto Embarque",
          "price": 960.00,
          "unit": "R$/saca 60kg"
        }
      }
    },
    "raw_text": "Texto extraído do PDF para depuração"
  }
}
```

## 5. Detalhes Técnicos

### 5.1 Integração com OpenRouter API

#### 5.1.1 Configuração do modelo de IA
- Usar modelo compatível com análise de documentos/PDF (como GPT-4 Vision ou Claude)
- Configurar prompt otimizado para identificação de cotações de café em documentos financeiros

#### 5.1.2 Estrutura do prompt
```
Você é um assistente especializado em extrair informações financeiras de documentos PDF do mercado cafeeiro brasileiro.
Extraia as cotações de café do PDF fornecido, identificando os seguintes tipos:

GRUPO I (BASE VARGINHA/MG):
- Tipo C. Interno $600 DEF. 11+$
- Tipo 6/7, BC Duro
- Tipo 6, BC Fino

GRUPO II (BASE VITÓRIA):
- Tipo C. Interno 600 DEF.
- Tipo 7, Bica
- Tipo 5/6 15/16 (Pronto Embarque)
- Tipo 2/3 17/18 (Pronto Embarque)

CONILLON (BASE VITÓRIA):
- Tipo 7 Bica Corrida
- Tipo 5/6 13 UP Pronto Embarque

Forneça as cotações em um formato JSON estruturado, incluindo:
- Valores em reais (R$)
- Unidade de medida (geralmente por saca de 60kg - R$/saca 60kg)
- Data da cotação se estiver disponível
- Base de cotação (Varginha/MG ou Vitória)

Responda estritamente no seguinte formato JSON:
{
  "base": "Varginha/MG ou Vitória",
  "date": "AAAA-MM-DD",
  "quotes": {
    "grupo_i": {
      "tipo_c_interno_600_def_11": {"name": "Tipo C. Interno $600 DEF. 11+$", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_6_7_bc_duro": {"name": "Tipo 6/7, BC Duro", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_6_bc_fino": {"name": "Tipo 6, BC Fino", "price": valor, "unit": "R$/saca 60kg"}
    },
    "grupo_ii": {
      "tipo_c_interno_600_def": {"name": "Tipo C. Interno 600 DEF.", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_7_bica": {"name": "Tipo 7, Bica", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_5_6_15_16_pronto_embarque": {"name": "Tipo 5/6 15/16 (Pronto Embarque)", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_2_3_17_18_pronto_embarque": {"name": "Tipo 2/3 17/18 (Pronto Embarque)", "price": valor, "unit": "R$/saca 60kg"}
    },
    "conillon": {
      "tipo_7_bica_corrida": {"name": "Tipo 7 Bica Corrida", "price": valor, "unit": "R$/saca 60kg"},
      "tipo_5_6_13_up_pronto_embarque": {"name": "Tipo 5/6 13 UP Pronto Embarque", "price": valor, "unit": "R$/saca 60kg"}
    }
  },
  "raw_data": "texto_bruto_extraído"
}
```

### 5.2 Estratégia de Processamento

#### 5.2.1 Validação de dados
- Verificar se os valores extraídos são razoáveis (não negativos, não excessivamente altos)
- Comparar com valores anteriores para detectar possíveis erros de extração
- Implementar mecanismos de verificação de integridade dos dados

#### 5.2.2 Tratamento de falhas
- Implementar fallback para valores anteriores em caso de falha na IA
- Implementar fallback para cotações simuladas se todos os métodos falharem
- Log detalhado de processamento para facilitar depuração

### 5.3 Otimização de custos

#### 5.3.1 Cache de resultados
- Implementar sistema de cache para evitar processamento repetido do mesmo PDF
- Armazenar resultados por até 24 horas ou até novo upload

#### 5.3.2 Estratégia de processamento
- Processar apenas quando houver mudança no arquivo
- Implementar fila de processamento para evitar múltiplas chamadas simultâneas

## 6. Segurança e Privacidade

### 6.1 Gerenciamento de Chaves
- Armazenar chave da API do OpenRouter em variáveis de ambiente
- Evitar exposição de credenciais no código fonte

### 6.2 Validação de Entrada
- Validar todos os uploads de PDF para evitar injeção de conteúdo malicioso
- Implementar validação de formato e tamanho máximo de arquivo

## 7. Testes e Qualidade

### 7.1 Testes Unitários
- Testar extração de diferentes formatos de cotações
- Testar tratamento de erros e fallbacks
- Validar formato de saída dos dados

### 7.2 Testes de Integração
- Testar fluxo completo de upload → processamento → exibição
- Validar funcionamento com diferentes exemplos de boletins PDF

## 8. Implementação do Frontend

### 8.1 Atualizar o componente de cotações

O componente `assets/js/components/quotes.js` precisa ser atualizado para:

1. Exibir as cotações específicas do mercado cafeeiro brasileiro
2. Organizar as cotações por grupo (GRUPO I, GRUPO II, CONILLON)
3. Mostrar informações de base geográfica
4. Manter o design visual atual do sistema

### 8.2 Interface de usuário
- Criar seções distintas para cada grupo de cotações
- Utilizar cards com gradientes específicos para cada tipo de café
- Adicionar informações de base geográfica (Varginha/MG ou Vitória)
- Manter animações e efeitos visuais do design atual

## 9. Considerações Técnicas Adicionais

### 9.1 Identificação de formato de PDF
- Implementar detecção de formato de boletim para garantir que o PDF contém as informações necessárias
- Verificar estrutura do documento antes de enviar para processamento
- Validar seções de cotações estão presentes no PDF

### 9.2 Processamento de múltiplos formatos
- Considerar que diferentes boletins podem ter estruturas diferentes
- Implementar lógica de detecção de formato para aplicar diferentes prompts à IA
- Manter um histórico de formatos conhecidos para otimizar processamento

### 9.3 Melhorias na interface
- Adicionar indicadores de status para mostrar quando as cotações estão sendo atualizadas
- Implementar notificações de sucesso ou falha na extração
- Criar histórico de cotações para comparação de preços

## 10. Monitoramento e Manutenção

### 10.1 Logging
- Registrar todas as chamadas à API de IA
- Manter logs de erros e falhas de processamento
- Monitorar tempo de resposta e custos da API

### 10.2 Performance
- Otimizar chamadas à API para minimizar custos
- Implementar sistema de cache eficiente
- Monitorar uso de recursos do servidor

## 11. Considerações Finais

Este plano permite implementar uma solução moderna e confiável para extração de cotações de café de PDFs usando inteligência artificial. A abordagem com OpenRouter oferece flexibilidade para usar diferentes modelos de IA especializados em análise de documentos, e o sistema inclui mecanismos de segurança e confiabilidade para garantir que o sistema continue funcional mesmo em caso de falhas na IA.

A implementação levará em consideração as categorias específicas de cotações do mercado cafeeiro brasileiro, como GRUPO I (BASE VARGINHA/MG), GRUPO II (BASE VITÓRIA) e CONILLON (BASE VITÓRIA), com seus respectivos tipos de café. A integração será feita de forma modular, permitindo manter a funcionalidade existente como fallback enquanto a nova funcionalidade é aprimorada.

O sistema considerará os detalhes específicos das cotações do mercado cafeeiro brasileiro, como:
- Unidade de medida: 60 kg, ensacado, posto armazém interior
- Valores em R$/saca e US$/saca
- Inclusão do Funrural, mas exclusão do ICMS
- Diferentes bases geográficas (Varginha/MG e Vitória)
- Distinção entre tipos específicos de café (BC Duro, BC Fino, Bica, etc.)

Além disso, o sistema será projetado para ser extensível, permitindo a inclusão de novos tipos de café ou categorias de cotações conforme necessário no futuro. A interface do usuário será atualizada para refletir de forma clara e organizada essas categorias específicas, mantendo o design visual agradável e informativo do sistema atual.

A implementação seguirá boas práticas de desenvolvimento, incluindo testes automatizados, documentação adequada e monitoramento contínuo para garantir a confiabilidade e segurança do sistema.