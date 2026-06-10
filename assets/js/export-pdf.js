// Export PDF menggunakan jsPDF + AutoTable
function exportPDF() {
    const { jsPDF } = window.jspdf;
    const config = window.exportPDFConfig || {};
    const orientation = config.orientation || 'portrait';
    const doc = new jsPDF({ orientation });

    const pageW = doc.internal.pageSize.getWidth();
    doc.setFontSize(16);
    doc.text('LUMIERE PARFUM', pageW / 2, 15, { align: 'center' });
    doc.setFontSize(9);
    doc.setTextColor(150);
    doc.text(config.subtitle || '', pageW / 2, 22, { align: 'center' });
    doc.setTextColor(0);

    doc.autoTable({
        startY: 28,
        head: [config.head],
        body: config.body,
        headStyles: { fillColor: [201, 169, 98] },
        styles: { fontSize: 8 },
        alternateRowStyles: { fillColor: [250, 248, 244] }
    });

    doc.save(config.filename || 'export.pdf');
}
