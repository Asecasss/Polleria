const mesas = Array.from({ length: 12 }, (_, i) => ({
  id: i + 1,
  estado: i % 3 === 0 ? 'ocupada' : 'disponible' // algunas ocupadas al inicio
}));

const categorias = {
  "Pollo a la Brasa": ["1 pollo entero", "1/2 Pollo", "1/4 Pollo", "1/8 Pollo", "Monstrito 1/8", "Monstrito 1/4",
  "Monstrito Kaypi", "Chaufa Brasa", "Mollejitas Fritas", "Aguadito", "P. Papa", "P. Ensalada", "P. Arroz Chaufa"],
  "Platos Dulces": ["Kanlu Wantan", "Chancho c/ Tamarindo", "Pollo con Piña", "Pollo c/ Piña - Durazno", "Tipakay",
  "Limon Kay", "Alitas en Salsa Tamarindo", "Enrollado Pollo + Chaufa", "Alitas Kaipy"],
  "Chaufas": ["Chaufa de Pollo", "Chaufa de Chancho", "Chaufa de Carne", "Chaufa de Langostinos", "Chaufa Mixto",
  "Chaufa Especial", "Chaufa Salvaje", "Chaufa Kaipy"],
  "Tallarines": ["Tallarines de Pollo", "Tallarines de Chancho", "Tallarines de Carne", "Tallarines Mixto",
  "Tallarines de Langostinos","Tallarines Especial", "Tallarines de Pollo c/ Verdura", "Tallarines Kaipy",
  "Tallarines 40x40", "Tallarin Kaipy"],
  "Caldos": ["Chaufa de Pollo", "Chaufa de Chancho", "Chaufa de Carne", "Chaufa de Langostinos", "Chaufa Mixto",
  "Chaufa Especial", "Chaufa Salvaje", "Chaufa Kaipy"],
};

let mesaSeleccionada = null;
let categoriaSeleccionada = null;
let pedido = [];

const mesasContainer = document.getElementById('mesas-container');
const pedidoPanel = document.getElementById('pedido-panel');
const mesaSeleccionadaNum = document.getElementById('mesa-seleccionada-num');
const categoriasDiv = document.getElementById('categorias');
const subcategoriasDiv = document.getElementById('subcategorias');
const listaPedido = document.getElementById('lista-pedido');
const btnEnviar = document.getElementById('btn-enviar');
const btnPedidoHecho = document.getElementById('btn-pedido-hecho');

function renderMesas() {
  mesasContainer.innerHTML = '';
  mesas.forEach(mesa => {
    const div = document.createElement('div');
    div.classList.add('mesa');

    // Aplicar clase según estado
    if (mesa.estado === 'ocupada') {
      div.classList.add('ocupada');
    } else {
      div.classList.add('disponible');
    }

    if (mesaSeleccionada === mesa.id) div.classList.add('seleccionada');
    div.innerHTML = `<i class="fas fa-chair"></i>Mesa ${mesa.id}`;
    div.onclick = () => seleccionarMesa(mesa.id);
    mesasContainer.appendChild(div);
  });
}


function seleccionarMesa(id) {
  mesaSeleccionada = id;
  pedido = [];
  categoriaSeleccionada = null;
  mesaSeleccionadaNum.textContent = id;
  pedidoPanel.style.display = 'block';
  renderMesas();
  renderCategorias();
  renderSubcategorias();
  renderPedido();

  const mesa = mesas.find(m => m.id === id);

  if (mesa.estado === 'ocupada') {
    btnEnviar.style.display = 'none';
    btnPedidoHecho.style.display = 'block';
  } else {
    btnEnviar.style.display = 'block';
    btnPedidoHecho.style.display = 'none';
  }
}

function renderCategorias() {
  categoriasDiv.innerHTML = '';
  Object.keys(categorias).forEach(cat => {
    const btn = document.createElement('button');
    btn.textContent = cat;
    btn.classList.add('categoria-btn');
    if (categoriaSeleccionada === cat) btn.classList.add('seleccionada');
    btn.onclick = () => {
      categoriaSeleccionada = cat;
      renderCategorias();
      renderSubcategorias();
    };
    categoriasDiv.appendChild(btn);
  });
}

function renderSubcategorias() {
  subcategoriasDiv.innerHTML = '';
  if (!categoriaSeleccionada) return;
  categorias[categoriaSeleccionada].forEach(subcat => {
    const btn = document.createElement('button');
    btn.classList.add('subcategoria-btn');
    btn.innerHTML = `<i class="fas fa-utensils"></i> ${subcat}`;
    btn.onclick = () => agregarPedido(subcat);
    subcategoriasDiv.appendChild(btn);
  });
}

function agregarPedido(item) {
  pedido.push(item);
  renderPedido();
}

function renderPedido() {
  listaPedido.innerHTML = '';
  pedido.forEach((item) => {
    const li = document.createElement('li');
    li.textContent = item;
    listaPedido.appendChild(li);
  });
}

btnEnviar.onclick = () => {
  if (!mesaSeleccionada) {
    alert('Selecciona una mesa primero.');
    return;
  }
  if (pedido.length === 0) {
    alert('Agrega al menos un pedido.');
    return;
  }

  alert(`✅ Pedido enviado para Mesa ${mesaSeleccionada}:\n- ${pedido.join('\n- ')}`);

  const mesa = mesas.find(m => m.id === mesaSeleccionada);
  mesa.estado = 'ocupada';

  resetPanel();
  renderMesas();
};

btnPedidoHecho.onclick = () => {
  if (!mesaSeleccionada) return;

  const mesa = mesas.find(m => m.id === mesaSeleccionada);
  mesa.estado = 'disponible';
  alert(`✅ Mesa ${mesaSeleccionada} ahora está libre.`);

  resetPanel();
  renderMesas();
};

function resetPanel() {
  mesaSeleccionada = null;
  pedido = [];
  categoriaSeleccionada = null;
  pedidoPanel.style.display = 'none';
  btnEnviar.style.display = 'block';
  btnPedidoHecho.style.display = 'none';
}

renderMesas();
