# 🎯 ZenScribe - Guia de Instalação no Bitrix24

## 📋 **Pré-requisitos**
- ✅ Portal Bitrix24 (conta admin)  
- ✅ Servidor web com PHP 8.0+ rodando (neste caso, localhost:8000)  
- ✅ Acesso às configurações de Desenvolvedor no Bitrix24  

---

## 🔧 **Passo 1: Criar Aplicação Local no Bitrix24**

### **1.1 Acessar área de Desenvolvedor:**
1. Entre no seu portal Bitrix24
2. Vá em **Aplicações** (menu superior)
3. Clique em **Desenvolvedor** (menu lateral esquerdo)
4. Escolha **Outros** → **Aplicação Local**

### **1.2 Preencher dados da aplicação:**
```
📝 Configuração da Aplicação:

Nome da aplicação: ZenScribe
Código da aplicação: zenscribe

🌐 URLs:
Caminho handler: http://localhost:8000/index.php
Caminho de instalação: http://localhost:8000/install.php

⚠️ IMPORTANTE: Para produção, substitua localhost:8000 pelo seu domínio real
```

### **1.3 Configurar Permissões (MUITO IMPORTANTE):**

**✅ Permissões obrigatórias:**
- **CRM** - Para acessar leads, deals, contatos, atividades
- **user** - Para identificar o usuário atual e suas informações
- **calendar** - Para acessar eventos do calendário (se disponível)

**❌ Permissões que NÃO existem/não usar:**
- ~~profile~~ - Esta permissão foi removida do Bitrix24
- ~~task~~ - Não necessário para o ZenScribe
- ~~im~~ - Não necessário para o ZenScribe

**🔍 Como encontrar as permissões:**
1. Na tela de criação do app, procure por "Permissões"
2. Marque apenas as checkboxes: **CRM**, **user**, **calendar**
3. Não se preocupe se "calendar" não aparecer - nem todos os portais têm

---

## 🚀 **Passo 2: Instalar e Configurar**

### **2.1 Instalar a aplicação:**
1. Clique em **"Instalar"** na tela de configuração
2. O Bitrix24 irá acessar `http://localhost:8000/install.php`
3. Você verá uma tela de sucesso se tudo estiver correto

### **2.2 Copiar credenciais:**
Após a instalação, você receberá:
```
Client ID: (algo como local.67891abc...)
Client Secret: (chave secreta única)
```

### **2.3 Configurar no ZenScribe:**
1. Acesse o ZenScribe pelo menu de aplicações do Bitrix24
2. Ou vá diretamente para `http://localhost:8000/index.php`
3. Configure as credenciais nas configurações avançadas

---

## 🐛 **Resolução de Problemas Comuns**

### **❌ "Aplicação não carrega"**
- ✅ Verifique se o servidor PHP está rodando
- ✅ Teste `http://localhost:8000/test.php` diretamente
- ✅ Confirme que as URLs no Bitrix24 estão corretas

### **❌ "Erro de permissões"**
- ✅ Verifique se marcou **CRM** e **user**
- ✅ Certifique-se de que você é admin do portal
- ✅ Tente recriar o app com permissões corretas

### **❌ "Permissão 'profile' não encontrada"**
- ✅ **IGNORE** - Esta permissão foi removida do Bitrix24
- ✅ Use apenas **CRM**, **user** e **calendar** (se disponível)

### **❌ "Access token invalid"**
- ✅ Verifique se Client ID/Secret estão corretos
- ✅ Reinstale o app se necessário
- ✅ Confirme que o webhook tem as permissões corretas

---

## 📊 **Estrutura de Permissões Detalhada**

### **🔐 CRM (Obrigatório)**
```
Permite acesso a:
- crm.lead.* (leads)
- crm.deal.* (negócios) 
- crm.contact.* (contatos)
- crm.company.* (empresas)
- crm.activity.* (atividades)
- crm.entity.fields (campos customizados)
```

### **👤 user (Obrigatório)**
```
Permite acesso a:
- user.current (usuário atual)
- user.get (informações do usuário)
- Identificação de quem está usando o app
```

### **📅 calendar (Opcional)**
```
Permite acesso a:
- calendar.* (eventos do calendário)
- Nem todos os portais têm esta permissão disponível
- Se não aparecer, pule esta etapa
```

---

## ✅ **Checklist Final**

- [ ] Servidor PHP rodando em localhost:8000
- [ ] App Local criado no Bitrix24
- [ ] Permissões CRM e user configuradas
- [ ] URLs handler/instalação corretas
- [ ] App instalado com sucesso
- [ ] Client ID/Secret copiados
- [ ] ZenScribe acessível pelo Bitrix24

---

## 🆘 **Suporte**

Se ainda tiver problemas:
1. Teste `http://localhost:8000/test.php` primeiro
2. Verifique os logs PHP se houver erros
3. Confirme que você tem permissões de admin no Bitrix24
4. Tente recriar o app do zero se necessário

---

**🎯 ZenScribe v2.0** - Transformando reuniões em insights acionáveis com IA.
