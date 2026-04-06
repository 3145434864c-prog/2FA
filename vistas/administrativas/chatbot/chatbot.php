<?php
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    die('<div class="alert alert-danger">Admin only</div>');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chatbot Inventarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        #chatContainer { height: 70vh; }
        #chatMessages { height: 80%; overflow-y: auto; background: #f8f9fa; }
        .msg-user { text-align: right; }
        .msg-bot { text-align: left; }
        .msg-bubble { max-width: 85%; display: inline-block; padding: 10px 15px; border-radius: 18px; word-wrap: break-word; text-align: left; }
        .msg-user .msg-bubble { background: #007bff; color: white; }
        .msg-bot .msg-bubble { background: white; border: 1px solid #dee2e6; color: #333; font-size: 14px; line-height: 1.6; }
        .msg-bot .msg-bubble strong { color: #4e73df; }
        .msg-bot .msg-bubble hr { margin: 4px 0; opacity: 0.2; }
        .msg-bot .msg-bubble table { font-size: 12px; }
        .typing { opacity: 0.6; font-style: italic; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h2><i class="fas fa-robot text-primary"></i> Asistente de Inventario</h2>
        <div class="row">
            <div class="col-12 col-lg-9">
                <div id="chatContainer" class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-circle text-success me-1" style="font-size:10px"></i> Asistente IA</span>
                        <span id="status" class="badge bg-success">Conectado</span>
                    </div>
                    <div id="chatMessages" class="p-3">
                        <div class="msg-bot mb-3">
                            <div class="msg-bubble">
                                👋 <strong>¡Hola! Soy tu asistente de inventario.</strong><br><br>
                                Puedo ayudarte con:<br>
                                • 🔍 Buscar productos por nombre o referencia<br>
                                • 📦 Consultar stock disponible<br>
                                • 📂 Filtrar por categoría<br>
                                • 💰 Ver valor total del inventario<br>
                                • ⚠️ Ver productos con stock bajo<br>
                                • 🚨 Ver productos agotados<br><br>
                                ¿Qué necesitas consultar?
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="input-group">
                            <input id="mensaje" class="form-control" placeholder="Escribe tu consulta..." maxlength="500">
                            <button id="enviar" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-header">
                        <strong>💡 Ejemplos</strong>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-outline-danger btn-sm w-100 mb-2" onclick="ejemplo('stock bajo')">⚠️ Stock bajo</button>
                        <button class="btn btn-outline-success btn-sm w-100 mb-2" onclick="ejemplo('valor total')">💰 Valor inventario</button>
                        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="ejemplo('buscar laptop')">🔍 Buscar producto</button>
                        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="ejemplo('categoria consolas')">📂 Por categoría</button>
                        <button class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="ejemplo('referencia PROD-0001')">🏷️ Por referencia</button>
                        <button class="btn btn-outline-warning btn-sm w-100 mb-2" onclick="ejemplo('productos agotados')">🚨 Agotados</button>
                        <button class="btn btn-outline-info btn-sm w-100 mb-2" onclick="ejemplo('todos')">📋 Ver todos</button>
                        <hr>
                        <button class="btn btn-outline-secondary btn-sm w-100" onclick="clearChat()">🗑️ Limpiar chat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function enviarMensaje() {
            const input = document.getElementById('mensaje');
            const msg = input.value.trim();
            if (!msg) return;

            input.value = '';
            document.getElementById('status').textContent = 'Procesando...';
            document.getElementById('enviar').disabled = true;

            const messages = document.getElementById('chatMessages');

            const divUser = document.createElement('div');
            divUser.className = 'msg-user mb-3';
            divUser.innerHTML = `<div class="msg-bubble">${escapeHtml(msg)}</div>`;
            messages.appendChild(divUser);

            const divTyping = document.createElement('div');
            divTyping.className = 'msg-bot mb-3 typing';
            divTyping.id = 'typing';
            divTyping.innerHTML = `<div class="msg-bubble">⏳ Consultando...</div>`;
            messages.appendChild(divTyping);
            scrollBottom();

            try {
                const formData = new FormData();
                formData.append('mensaje', msg);

                const resp = await fetch('/2FA/index.php?route=chatbot', {
                    method: 'POST',
                    body: formData
                });

                const data = await resp.json();
                document.getElementById('typing')?.remove();

                const divBot = document.createElement('div');
                divBot.className = 'msg-bot mb-3';

                const esTabla = data.tabla && Array.isArray(data.tabla) && data.tabla.length > 0;
                divBot.innerHTML = `<div class="msg-bubble">${formatearRespuesta(data.respuesta || 'Error')}${esTabla ? generarTabla(data.tabla) : ''}</div>`;
                messages.appendChild(divBot);
                scrollBottom();

            } catch (err) {
                console.error('Error:', err);
                document.getElementById('typing')?.remove();
                const divErr = document.createElement('div');
                divErr.className = 'msg-bot mb-3';
                divErr.innerHTML = `<div class="msg-bubble bg-danger text-white">❌ Error de conexión</div>`;
                messages.appendChild(divErr);
            } finally {
                document.getElementById('status').textContent = 'Conectado';
                document.getElementById('enviar').disabled = false;
                input.focus();
            }
        }

        function formatearRespuesta(text) {
            const map = {'&':'&amp;','<':'<','>':'>','"':'"',"'":'&#039;'};
            let html = text.replace(/[&<>"']/g, m => map[m]);
            html = html.replace(/\*([^*]+)\*/g, '<strong>$1</strong>');
            html = html.replace(/─+/g, '<hr>');
            html = html.replace(/\n/g, '<br>');
            return html;
        }

        function generarTabla(productos) {
            if (!productos || productos.length === 0) return '';
            const filas = productos.map(p => `
                <tr>
                    <td>${p.nombre}</td>
                    <td><span class="badge bg-secondary">${p.referencia}</span></td>
                    <td><span class="badge ${p.stock <= 5 ? 'bg-danger' : 'bg-success'}">${p.stock}</span></td>
                    <td>$${Number(p.precio).toLocaleString('es-CO')}</td>
                </tr>`).join('');
            return `
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered table-hover mb-0" style="font-size:12px;background:white;">
                    <thead class="table-primary">
                        <tr><th>Producto</th><th>Ref</th><th>Stock</th><th>Precio</th></tr>
                    </thead>
                    <tbody>${filas}</tbody>
                </table>
            </div>`;
        }

        function escapeHtml(text) {
            const map = {'&':'&amp;','<':'<','>':'>','"':'"',"'":'&#039;'};
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function ejemplo(txt) {
            document.getElementById('mensaje').value = txt;
            enviarMensaje();
        }

        function clearChat() {
            document.getElementById('chatMessages').innerHTML = `
                <div class="msg-bot mb-3">
                    <div class="msg-bubble">
                        👋 <strong>Chat reiniciado.</strong><br>¿En qué puedo ayudarte?
                    </div>
                </div>`;
        }

        function scrollBottom() {
            const m = document.getElementById('chatMessages');
            m.scrollTop = m.scrollHeight;
        }

        document.getElementById('enviar').onclick = enviarMensaje;
        document.getElementById('mensaje').addEventListener('keypress', e => e.key === 'Enter' && enviarMensaje());
    </script>
</body>
</html>
