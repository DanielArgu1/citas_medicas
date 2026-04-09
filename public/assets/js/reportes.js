document.addEventListener('DOMContentLoaded', function () {
    const dataNode = document.getElementById('reportes-data');
    let reportesData = { porEstado: [], diagnosticosFrecuentes: [] };

    if (dataNode) {
        try {
            reportesData = JSON.parse(dataNode.textContent);
        } catch (error) {
            console.error('No se pudo leer la data de reportes.', error);
        }
    }

    const estadosCanvas = document.getElementById('chartEstados');
    if (estadosCanvas && window.Chart) {
        new Chart(estadosCanvas, {
            type: 'doughnut',
            data: {
                labels: reportesData.porEstado.map(item => item.estado),
                datasets: [{
                    data: reportesData.porEstado.map(item => Number(item.total)),
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981', '#ef4444'],
                    hoverOffset: 12
                }]
            },
            options: { responsive: true, animation: { animateScale: true, animateRotate: true } }
        });
    }

    const diagnosticosCanvas = document.getElementById('chartDiagnosticos');
    if (diagnosticosCanvas && window.Chart) {
        new Chart(diagnosticosCanvas, {
            type: 'bar',
            data: {
                labels: reportesData.diagnosticosFrecuentes.map(item => item.diagnostico),
                datasets: [{
                    data: reportesData.diagnosticosFrecuentes.map(item => Number(item.total)),
                    backgroundColor: '#2563eb'
                }]
            },
            options: { responsive: true, animation: { duration: 1200, easing: 'easeOutBounce' } }
        });
    }

    async function exportToPDF(elementId, filename) {
        const { jsPDF } = window.jspdf;
        const element = document.getElementById(elementId);
        if (!element || !window.html2canvas || !window.jspdf) {
            return;
        }
        const canvas = await html2canvas(element, { scale: 2 });
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'pt', 'a4');
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.setFontSize(10);
        pdf.text('Reporte generado: ' + new Date().toLocaleString(), 20, pdf.internal.pageSize.getHeight() - 10);
        pdf.save(filename + '_' + new Date().toISOString().slice(0, 10) + '.pdf');
    }

    async function imprimirDashboard() {
        const charts = document.querySelectorAll('canvas');

        for (const canvas of charts) {
            const canvasImg = await html2canvas(canvas.parentElement, { scale: 2 });
            const img = document.createElement('img');
            img.src = canvasImg.toDataURL('image/png');
            img.style.width = '100%';
            canvas.style.display = 'none';
            canvas.parentElement.appendChild(img);
        }

        window.print();

        for (const canvas of charts) {
            canvas.style.display = 'block';
            const imgs = canvas.parentElement.querySelectorAll('img');
            imgs.forEach(img => img.remove());
        }
    }

    document.querySelectorAll('[data-export]').forEach(button => {
        button.addEventListener('click', async function () {
            const type = this.getAttribute('data-export');
            const target = this.getAttribute('data-target');
            const filename = this.getAttribute('data-filename') || 'Reporte';
            const wrapper = document.getElementById(target);
            if (!wrapper) {
                return;
            }

            if (type === 'excel' && window.XLSX) {
                const table = wrapper.querySelector('table');
                const wb = XLSX.utils.table_to_book(table, { sheet: 'Sheet1' });
                XLSX.writeFile(wb, filename + '_' + new Date().toISOString().slice(0, 10) + '.xlsx');
            }

            if (type === 'pdf') {
                await exportToPDF(target, filename);
            }
        });
    });

    document.querySelectorAll('[data-report-action="print"]').forEach(button => {
        button.addEventListener('click', imprimirDashboard);
    });
});
