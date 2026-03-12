<?php if (!empty($_SESSION['usuario_id'])): ?>
        </section>
    </main>
</div>
<?php endif; ?>
<script>
document.querySelectorAll('[data-count]').forEach((element)=>{const target=parseInt(element.getAttribute('data-count'),10)||0;let current=0;const inc=Math.max(1,Math.ceil(target/35));const timer=setInterval(()=>{current+=inc;if(current>=target){current=target;clearInterval(timer)}element.textContent=current},20)});
document.querySelectorAll('[data-search-target]').forEach((input)=>{input.addEventListener('keyup',function(){const targetId=this.getAttribute('data-search-target');const query=this.value.toLowerCase().trim();document.querySelectorAll('#'+targetId+' tr').forEach((row)=>{row.classList.toggle('hidden-row',!row.textContent.toLowerCase().includes(query))})})});
document.querySelectorAll('[data-confirm]').forEach((a)=>a.addEventListener('click',function(e){if(!confirm(this.getAttribute('data-confirm')||'¿Estás seguro?'))e.preventDefault()}));
function soloNumeros(v){return v.replace(/\D/g,'')}
function mascaraCedula(el){let v=soloNumeros(el.value).slice(0,13);if(v.length>8)v=v.slice(0,4)+'-'+v.slice(4,8)+'-'+v.slice(8);else if(v.length>4)v=v.slice(0,4)+'-'+v.slice(4);el.value=v}
function mascaraTelefono(el){let v=soloNumeros(el.value).slice(0,8);if(v.length>4)v=v.slice(0,4)+'-'+v.slice(4);el.value=v}
document.querySelectorAll('[data-mask="cedula"]').forEach((el)=>el.addEventListener('input',()=>mascaraCedula(el)));
document.querySelectorAll('[data-mask="telefono"]').forEach((el)=>el.addEventListener('input',()=>mascaraTelefono(el)));
</script>
</body>
</html>

<script>
function soloLetras(v){
    return v.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '').replace(/\s+/g, ' ');
}

document.querySelectorAll('[data-only-letters]').forEach((el) => {
    el.addEventListener('input', () => {
        el.value = soloLetras(el.value);
    });
});
</script>