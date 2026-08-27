<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD AJAX - Registros</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }
        header {
            background: #20252b;
            color: white;
            padding: 22px;
        }
        header h1 { margin: 0 0 5px; }
        header p { margin: 0; color: #cfd4da; }
        main { max-width: 1400px; margin: 32px auto; padding: 0 24px; }
        .panel {
            background: white;
            border-radius: 12px;
            padding: 28px;
            box-shadow: 0 2px 10px rgba(0,0,0,.08);
            margin-bottom: 22px;
        }
        form {
            display: grid;
            grid-template-columns: 2fr 1fr 2fr auto;
            gap: 12px;
            align-items: end;
        }
        .formulario-inline {
            grid-template-columns: 1fr;
            align-items: stretch;
        }
        .valor-inline {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            width: 100%;
        }
        .valor-inline input {
            min-width: 0;
        }
        .valor-inline button { width: 100%; min-height: 44px; }
        .valor-inline .actions { width: 100%; }
        .valor-inline .actions button { width: auto; }
        label { display: block; font-weight: bold; margin-bottom: 6px; }
        input[type="text"], input[type="number"], input[type="file"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #bbb;
            border-radius: 7px;
            background: white;
            font: inherit;
            resize: vertical;
        }
        textarea { min-height: 110px; }
        button {
            border: 0;
            border-radius: 7px;
            padding: 12px 18px;
            min-height: 44px;
            cursor: pointer;
            font-weight: bold;
        }
        .primary { background: #1f6feb; color: white; }
        .secondary { background: #e9ecef; }
        .danger { background: #dc3545; color: white; }
        .edit { background: #ffc107; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .lista-valores {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 16px;
        }
        .valor {
            min-height: 92px;
            padding: 18px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }
        .valor > span { overflow-wrap: anywhere; }
        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,.09);
        }
        .card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
            background: #eee;
        }
        .content { padding: 20px; }
        .content h3 { margin: 0 0 8px; font-size: 18px; }
        .number {
            font-size: 14px;
            color: #555;
            margin-bottom: 13px;
        }
        .actions { display: flex; gap: 8px; }
        .actions button { flex: 1; }
        #estado { margin-top: 10px; min-height: 20px; }
        .success { color: #16803c; }
        .error { color: #c92a2a; }
        .loading { opacity: .55; pointer-events: none; }
        .empty { text-align: center; padding: 30px; color: #666; }
        .contador { color: #555; margin: 0 0 14px; }
        @media (max-width: 800px) {
            form { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<header>
    <h1>CRUD de equipos con AJAX</h1>
    <p>Registros separados en tres bloques</p>
</header>

<main>
    <section class="panel">
        <h2 id="tituloFormulario">Agregar registro</h2>

        <form id="formulario" enctype="multipart/form-data">
            <div>
                <label for="texto">Texto</label>
                <input id="texto" name="texto" type="text" maxlength="300" required placeholder="Escribe hasta 300 caracteres">
            </div>

            <div>
                <label for="numero">Número</label>
                <input id="numero" name="numero" type="number" min="0" max="300" required placeholder="Ej. 25">
            </div>

            <div>
                <label for="imagen">Imagen</label>
                <input id="imagen" name="imagen" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            </div>

            <div>
                <button class="primary" type="submit" id="btnGuardar">Guardar</button>
                <button class="secondary" type="button" id="btnCancelar" hidden>Cancelar</button>
            </div>
        </form>

        <div id="estado" aria-live="polite"></div>
    </section>

    <section class="panel">
        <h2>Textos</h2>
        <p class="contador">Hasta 300 caracteres por registro</p>
        <div id="textos" class="lista-valores"></div>
    </section>

    <section class="panel">
        <h2>Números</h2>
        <p class="contador">300 valores iniciales</p>
        <div id="numeros" class="lista-valores"></div>
    </section>

    <section class="panel">
        <h2>Imágenes</h2>
        <p class="contador" id="contador">Cargando...</p>
        <div id="registros" class="grid"></div>
    </section>
</main>

<script>
let editandoId = null;

const formulario = document.getElementById('formulario');
const textos = document.getElementById('textos');
const numeros = document.getElementById('numeros');
const registros = document.getElementById('registros');
const estado = document.getElementById('estado');
const contador = document.getElementById('contador');
const tituloFormulario = document.getElementById('tituloFormulario');
const btnGuardar = document.getElementById('btnGuardar');
const btnCancelar = document.getElementById('btnCancelar');

function mostrarEstado(mensaje, tipo = '') {
    estado.textContent = mensaje;
    estado.className = tipo;
}

async function cargarRegistros() {
    try {
        const respuesta = await fetch('api/listar.php');
        if (!respuesta.ok) throw new Error('No se pudieron cargar los registros.');

        const datos = await respuesta.json();
        renderizarRegistros(datos);
    } catch (error) {
        mostrarEstado(error.message, 'error');
    }
}

function renderizarRegistros(datos) {
    contador.textContent = `${Math.min(datos.length, 30)} imágenes cargadas`;

    textos.innerHTML = datos.map(registro => `
        <div class="valor" id="texto-${registro.id}">
            <span>${escaparHTML(registro.texto)}</span>
            <div class="actions">
                <button class="edit" type="button" onclick="editarValor(${registro.id}, 'texto')">Editar texto</button>
                <button class="danger" type="button" onclick="eliminarRegistro(${registro.id})">Eliminar</button>
            </div>
        </div>
    `).join('');

    numeros.innerHTML = datos.map(registro => `
        <div class="valor" id="numero-${registro.id}">
            <strong>${registro.numero}</strong>
            <div class="actions">
                <button class="edit" type="button" onclick="editarValor(${registro.id}, 'numero')">Editar número</button>
                <button class="danger" type="button" onclick="eliminarRegistro(${registro.id})">Eliminar</button>
            </div>
        </div>
    `).join('');

    if (!datos.length) {
        registros.innerHTML = '<div class="empty">No hay registros.</div>';
        return;
    }

    registros.innerHTML = datos.slice(0, 30).map(registro => tarjetaHTML(registro)).join('');
}

function editarValor(id, tipo) {
    const valor = document.getElementById(`${tipo}-${id}`);
    const texto = document.querySelector(`#texto-${id} span`).textContent;
    const numero = document.querySelector(`#numero-${id} strong`).textContent;
    const campo = tipo === 'texto'
        ? `<textarea name="texto" rows="4" maxlength="300" required>${escaparHTML(texto)}</textarea>`
        : `<input name="numero" type="number" min="0" max="300" value="${numero}" required>`;
    const oculto = tipo === 'texto'
        ? `<input name="numero" type="hidden" value="${numero}">`
        : `<input name="texto" type="hidden" value="${escaparHTML(texto)}">`;

    valor.innerHTML = `
        <form class="formulario-inline valor-inline" data-id="${id}">
            ${campo}
            ${oculto}
            <div class="actions">
                <button class="primary" type="submit">Guardar</button>
                <button class="secondary" type="button" onclick="cargarRegistros()">Cancelar</button>
            </div>
        </form>
    `;

    valor.querySelector('textarea, input:not([type="hidden"])').focus();
}

document.querySelectorAll('.lista-valores').forEach((lista) => {
    lista.addEventListener('submit', async (event) => {
        if (!event.target.matches('.valor-inline')) return;

        event.preventDefault();
        const formularioValor = event.target;
        const datos = new FormData(formularioValor);
        datos.append('id', formularioValor.dataset.id);
        formularioValor.classList.add('loading');

        try {
            const respuesta = await fetch('api/editar.php', {
                method: 'POST',
                body: datos
            });
            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.ok) {
                throw new Error(resultado.mensaje || 'No se pudo actualizar.');
            }

            await cargarRegistros();
            mostrarEstado(resultado.mensaje, 'success');
        } catch (error) {
            mostrarEstado(error.message, 'error');
            formularioValor.classList.remove('loading');
        }
    });
});

function tarjetaHTML(registro) {
    const textoSeguro = escaparHTML(registro.texto);

    return `
        <article class="card" id="registro-${registro.id}">
            <img src="uploads/${encodeURIComponent(registro.imagen)}" alt="${textoSeguro}">
            <div class="content">
                <h3>${textoSeguro}</h3>
                <div class="number">Número: <strong>${registro.numero}</strong></div>
                <div class="actions">
                    <button class="edit" type="button" onclick="editarEnLinea(${registro.id})">Editar</button>
                    <button class="danger" type="button" onclick="eliminarRegistro(${registro.id})">Eliminar</button>
                </div>
            </div>
        </article>
    `;
}

function editarEnLinea(id) {
    const tarjeta = document.getElementById(`registro-${id}`);
    const titulo = tarjeta.querySelector('h3').textContent;
    const numero = tarjeta.querySelector('.number strong').textContent;

    tarjeta.querySelector('.content').innerHTML = `
        <form class="formulario-inline" data-id="${id}">
            <label>
                Texto
                <textarea name="texto" rows="3" maxlength="300" required>${escaparHTML(titulo)}</textarea>
            </label>
            <label>
                Número
                <input name="numero" type="number" min="0" max="300" value="${numero}" required>
            </label>
            <label>
                Reemplazar imagen
                <input name="imagen" type="file" accept="image/jpeg,image/png,image/gif,image/webp">
            </label>
            <div class="actions">
                <button class="primary" type="submit">Guardar</button>
                <button class="secondary" type="button" onclick="cargarRegistros()">Cancelar</button>
            </div>
        </form>
    `;
}

registros.addEventListener('submit', async (event) => {
    if (!event.target.matches('.formulario-inline')) return;

    event.preventDefault();
    const formularioInline = event.target;
    const datos = new FormData(formularioInline);
    datos.append('id', formularioInline.dataset.id);
    formularioInline.classList.add('loading');

    try {
        const respuesta = await fetch('api/editar.php', {
            method: 'POST',
            body: datos
        });
        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.ok) {
            throw new Error(resultado.mensaje || 'No se pudo actualizar.');
        }

        await cargarRegistros();
        mostrarEstado(resultado.mensaje, 'success');
    } catch (error) {
        mostrarEstado(error.message, 'error');
        formularioInline.classList.remove('loading');
    }
});

function escaparHTML(texto) {
    return String(texto)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function escaparJS(texto) {
    return String(texto)
        .replaceAll('\\', '\\\\')
        .replaceAll("'", "\\'");
}

formulario.addEventListener('submit', async (event) => {
    event.preventDefault();

    const datos = new FormData(formulario);
    const url = editandoId ? 'api/editar.php' : 'api/crear.php';

    if (editandoId) {
        datos.append('id', editandoId);
    }

    formulario.classList.add('loading');
    mostrarEstado(editandoId ? 'Actualizando...' : 'Guardando...');

    try {
        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.ok) {
            throw new Error(resultado.mensaje || 'Ocurrió un error.');
        }

        await cargarRegistros();

        mostrarEstado(resultado.mensaje, 'success');
        cancelarEdicion();
    } catch (error) {
        mostrarEstado(error.message, 'error');
    } finally {
        formulario.classList.remove('loading');
    }
});

function agregarTarjeta(registro) {
    const vacio = registros.querySelector('.empty');
    if (vacio) vacio.remove();

    registros.insertAdjacentHTML('afterbegin', tarjetaHTML(registro));
    textos.insertAdjacentHTML('afterbegin', `<div class="valor">${escaparHTML(registro.texto)}</div>`);
    numeros.insertAdjacentHTML('afterbegin', `<div class="valor"><strong>${registro.numero}</strong></div>`);
}

function actualizarTarjeta(registro) {
    const anterior = document.getElementById(`registro-${registro.id}`);
    if (anterior) {
        anterior.outerHTML = tarjetaHTML(registro);
    }

    cargarRegistros();
}

function prepararEdicion(id, texto, numero) {
    editandoId = id;
    document.getElementById('texto').value = texto;
    document.getElementById('numero').value = numero;
    document.getElementById('imagen').value = '';

    tituloFormulario.textContent = `Editar registro #${id}`;
    btnGuardar.textContent = 'Actualizar';
    btnCancelar.hidden = false;

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function cancelarEdicion() {
    editandoId = null;
    formulario.reset();
    tituloFormulario.textContent = 'Agregar registro';
    btnGuardar.textContent = 'Guardar';
    btnCancelar.hidden = true;
}

btnCancelar.addEventListener('click', cancelarEdicion);

async function eliminarRegistro(id) {
    if (!confirm(`¿Eliminar el registro #${id}?`)) return;

    const datos = new FormData();
    datos.append('id', id);

    try {
        const respuesta = await fetch('api/eliminar.php', {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if (!respuesta.ok || !resultado.ok) {
            throw new Error(resultado.mensaje || 'No se pudo eliminar.');
        }

        const tarjeta = document.getElementById(`registro-${id}`);
        if (tarjeta) tarjeta.remove();

        mostrarEstado(resultado.mensaje, 'success');
        await cargarRegistros();
    } catch (error) {
        mostrarEstado(error.message, 'error');
    }
}

cargarRegistros();
</script>
</body>
</html>
