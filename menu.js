function menu() {

    const itens = [
        { title: 'Movimentos', link: 'movimentos.html' },
        { title: 'Contas', link: 'contas.html' },
        { title: 'Tipos Movimentos', link: 'tiposMovimentos.html' },
    ]

    let menu = '';
    itens.forEach(element => {
        menu += `<a class="btn" href="${element.link}">${element.title}</a>`;
    });

    return menu;
}