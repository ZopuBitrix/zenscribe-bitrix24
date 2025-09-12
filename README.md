# 🎯 ZenScribe - AI Meeting Processor para Bitrix24

**Processamento inteligente de reuniões com IA integrado nativamente ao Bitrix24**

![ZenScribe](https://img.shields.io/badge/ZenScribe-v2.0.0-blue)
![Bitrix24](https://img.shields.io/badge/Bitrix24-Local%20App-green)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple)
![OpenAI](https://img.shields.io/badge/OpenAI-GPT--4-orange)

---

## ✨ **Características**

### 🚀 **Core Features:**
- 🎭 **Interface nativa** dentro do Bitrix24
- 🧠 **Processamento IA** com OpenAI GPT-4
- 📅 **Integração Google Calendar** e Drive
- 🎯 **Atividades customizadas** no CRM
- 📊 **Dashboard** com estatísticas em tempo real
- 🔄 **Auto-agendamento** de follow-ups
- 📱 **Responsive design** para mobile

### 🔧 **Tecnologias:**
- **Backend**: PHP 7.4+ com Bitrix24 REST API
- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **AI**: OpenAI GPT-4o-mini para análise semântica
- **Integrations**: Google Calendar, Google Drive, Bitrix24 CRM

---

## 🏗️ **Arquitetura**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Google APIs   │    │   ZenScribe App  │    │   Bitrix24 CRM  │
│                 │    │                  │    │                 │
│ • Calendar API  │◄──►│ • handler.php    │◄──►│ • Leads/Deals   │
│ • Drive API     │    │ • AI Processing  │    │ • Activities    │
│ • Docs API      │    │ • UI Interface   │    │ • Webhooks      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌──────────────────┐
                       │    OpenAI API    │
                       │                  │
                       │ • GPT-4o-mini    │
                       │ • Smart Analysis │
                       └──────────────────┘
```

---

## 📋 **Instalação**

### **📦 Pré-requisitos:**
- ✅ Portal Bitrix24 (conta admin)
- ✅ Servidor web com PHP 7.4+
- ✅ Google Cloud Console (APIs habilitadas)
- ✅ OpenAI API Key (opcional, mas recomendado)

### **🔧 Passo 1: Preparar Servidor**

```bash
# Upload dos arquivos para seu servidor web
# Exemplo: /var/www/html/zenscribe/

# Definir permissões
chmod 755 /var/www/html/zenscribe/
chmod 777 /var/www/html/zenscribe/logs/
chmod 777 /var/www/html/zenscribe/temp/
```

### **🌐 Passo 2: Google Cloud Console**

1. **Acesse**: https://console.cloud.google.com/
2. **Crie projeto** (ou use existente)
3. **Habilite APIs**:
   - Google Calendar API
   - Google Drive API  
   - Google Docs API
4. **Crie credenciais**:
   - Tipo: "Web application"
   - Redirect URI: `http://seudominio.com/zenscribe/oauth_callback.php`
5. **Anote**: Client ID e Client Secret

### **🏢 Passo 3: Criar App Local no Bitrix24**

1. **Acesse**: Seu portal > Aplicações > Desenvolvedor > Outros > Aplicação Local
2. **Preencha**:
   - **Nome**: ZenScribe
   - **Código**: `zenscribe`
   - **Caminho handler**: `http://seudominio.com/zenscribe/index.php`
   - **Caminho instalação**: `http://seudominio.com/zenscribe/install.php`
   - **Permissões**: CRM (crm), Usuário (user), Perfil (profile)
3. **Anote**: Client ID e Client Secret do app
4. **Instale** o app

### **⚙️ Passo 4: Configurar Credenciais**

1. **Edite**: `settings.php`
```php
define('ZENSCRIBE_CLIENT_ID', 'SEU_BITRIX24_CLIENT_ID');
define('ZENSCRIBE_CLIENT_SECRET', 'SEU_BITRIX24_CLIENT_SECRET');
```

2. **Acesse**: Interface do ZenScribe no Bitrix24
3. **Configure**:
   - Google Client ID/Secret
   - OpenAI API Key (opcional)
   - Preferências de processamento

---

## 🎯 **Como Usar**

### **📋 Fluxo Básico:**

1. **📅 Realizar reunião** no Google Meet
2. **📝 Aguardar transcrição** aparecer no Google Drive
3. **🎯 Abrir ZenScribe** no Bitrix24
4. **🚀 Clicar "Processar Última Reunião"**
5. **✅ Verificar resultados** no CRM

### **🔍 Detecção Automática:**

#### **URLs na descrição do evento:**
```
Reunião com cliente.
Link: https://portal.bitrix24.com.br/crm/lead/details/12345/
```
→ **Atualiza Lead #12345**

#### **Padrões alternativos:**
```
Follow-up Deal #67890
```
→ **Atualiza Deal #67890**

#### **Sem detecção:**
→ **Cria novo Lead** (padrão configurável)

### **🧠 Processamento IA (OpenAI):**

**Com OpenAI configurado:**
- 🎯 Extração precisa de dados
- 😰 Identificação de dores do cliente
- ✨ Análise de desejos/necessidades
- 💰 Detecção de valores mencionados
- 🎭 Criação de atividades ricas

**Sem OpenAI:**
- 🔍 Heurísticas básicas
- 📋 Extração de padrões conhecidos
- 💼 Funcionalidade essencial mantida

---

## 📊 **Interface**

### **🏠 Dashboard Principal:**
- 🚀 Botão "Processar Última Reunião"
- 📊 Estatísticas em tempo real
- ⚙️ Status das configurações
- 📋 Atividades recentes

### **⚙️ Configurações:**
- 🔗 Credenciais Google/OpenAI
- 🎯 Preferências de processamento
- 📱 Opções de auto-agendamento
- 🧪 Testes de conexão

### **📈 Estatísticas:**
- 📅 Reuniões processadas (hoje/mês)
- ✅ Taxa de sucesso
- 🎭 Atividades criadas
- 📊 Performance geral

---

## 🔧 **Configurações Avançadas**

### **🎯 Processamento:**
```php
// user_config.json
{
  "processing": {
    "auto_scheduling": true,
    "auto_contact_creation": true,
    "default_entity": "lead"
  }
}
```

### **🤖 OpenAI:**
```php
{
  "openai": {
    "api_key": "sk-proj-...",
    "model": "gpt-4o-mini",
    "enabled": true
  }
}
```

### **📅 Google APIs:**
```php
{
  "google": {
    "client_id": "236047145381-....apps.googleusercontent.com",
    "client_secret": "GOCSPX-...",
    "redirect_uri": "http://seudominio.com/zenscribe/oauth_callback.php"
  }
}
```

---

## 🐛 **Troubleshooting**

### **❌ Problemas Comuns:**

#### **"App não aparece no Bitrix24"**
- ✅ Verificar Client ID/Secret corretos
- ✅ Verificar URLs acessíveis publicamente
- ✅ Verificar permissões (CRM, user, profile)

#### **"Google OAuth falha"**
- ✅ APIs habilitadas no Google Cloud
- ✅ Redirect URI correto
- ✅ Client ID/Secret válidos

#### **"OpenAI não funciona"**
- ✅ API Key válida
- ✅ Créditos disponíveis
- ✅ Modelo correto (gpt-4o-mini)

#### **"Transcrição não encontrada"**
- ✅ Google Meet gerou transcrição
- ✅ Arquivo está no Google Drive
- ✅ Permissões de acesso ao Drive

### **🔍 Debug:**

#### **Logs:**
```bash
tail -f logs/zenscribe_$(date +%Y-%m-%d).log
```

#### **Health Check:**
```
GET /zenscribe/api.php?action=health_check
```

#### **Export Logs:**
Interface → Estatísticas → "📁 Exportar Logs"

---

## 🚀 **Roadmap**

### **📅 v2.1 (Próxima):**
- 🔗 Webhook automático Google Calendar
- 📱 Notificações push no Bitrix24
- 🎨 Temas customizáveis
- 📊 Relatórios avançados

### **📅 v2.2 (Futuro):**
- 🎥 Processamento de vídeo/áudio
- 🌐 Multi-idioma
- 🔄 Integrações adicionais (Slack, Teams)
- 🤖 IA personalizada por empresa

---

## 📝 **Licença**

MIT License - Uso livre para projetos comerciais e pessoais.

---

## 🆘 **Suporte**

- 📧 **Email**: suporte@zenscribe.app
- 📚 **Documentação**: https://docs.zenscribe.app
- 🐛 **Issues**: GitHub Issues
- 💬 **Community**: Discord/Telegram

---

**🎯 ZenScribe v2.0.0** - Transformando reuniões em insights acionáveis com IA.
