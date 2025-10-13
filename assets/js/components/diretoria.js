/**
 * Componente de Gerenciamento de Diretorias
 * Responsável por exibir as diretorias atuais e históricas
 * Dados incorporados diretamente no código para melhor desempenho
 */

class GerenciadorDiretorias {
    constructor() {
        // Dados das diretorias incorporados diretamente no código
        this.dadosDiretorias = {
    "diretoria_atual": {
        "periodo": "2016-2017",
        "presidente": "Guilherme Braga Abreu P. Filho",
        "diretores": [
            {
                "cargo": "Diretor Secretário",
                "nome": "Alexandre Todeschini Pires"
            },
            {
                "cargo": "Diretor Tesoureiro",
                "nome": "Batista Mancini"
            },
            {
                "cargo": "Diretor de Patrimônio",
                "nome": "Ruy Barreto Filho"
            },
            {
                "cargo": "Gerente-Geral",
                "nome": "Guilherme Braga Abreu P. Neto"
            }
        ]
    },
    "diretorias_historicas": [
        {
            "periodo": "2014-2015",
            "presidente": "Guilherme Braga Abreu P. Filho",
            "diretores": []
        },
        {
            "periodo": "2005-2007",
            "presidente": "Guilherme Braga Abreu P. Filho",
            "diretores": [
                {
                    "cargo": "Diretor Secretário",
                    "nome": "Batista Mancini"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Cesar Calmon"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Ruy Barreto Filho"
                },
                {
                    "cargo": "Superintendente",
                    "nome": "Guilherme Braga Abreu P. Neto"
                }
            ]
        },
        {
            "periodo": "2003-2005",
            "presidente": "Guilherme Braga Abreu P. Filho",
            "diretores": [
                {
                    "cargo": "Diretor Secretário",
                    "nome": "Batista Mancini"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Cesar Calmon"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Ruy Barreto Filho"
                },
                {
                    "cargo": "Superintendente",
                    "nome": "José Avelino Perácio de Freitas"
                }
            ]
        },
        {
            "periodo": "2001-2002",
            "presidente": "Guilherme Braga Abreu Pires Filho",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Batista Mancini"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Carlos Eduardo C. M. Portella"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Manoel Aranha Correa do Lago"
                }
            ]
        },
        {
            "periodo": "1999-2001",
            "presidente": "Guilherme Braga Abreu Pires Filho",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Batista Mancini"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Carlos Eduardo C. M. Portella"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Luiz Otavio Araripe"
                }
            ]
        },
        {
            "periodo": "1997-1999",
            "presidente": "José Avelino Peracio de Freitas",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Batista Mancini"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Guilherme Braga Abreu Pires Filho"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Carlos Eduardo C. M. Portella"
                }
            ]
        },
        {
            "periodo": "1993-1994",
            "presidente": "Sérgio Giestas Tristão",
            "diretores": [
                {
                    "cargo": "Vice-Presidente",
                    "nome": "Frederico Balsemão Pires"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Eduardo Neves Peracio de Freitas"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Manoel Aranha Corrêa do Lago"
                }
            ]
        },
        {
            "periodo": "1991-1992",
            "presidente": "Orlando Corrêa Neto",
            "diretores": [
                {
                    "cargo": "Vice-Presidente",
                    "nome": "Sérgio Giestas Tristão"
                },
                {
                    "cargo": "Secretário",
                    "nome": "Raphael José de Oliveira B. Neto"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Hermogenes Teixeira Ladeira"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Manoel Pereira da Silva Leite"
                }
            ]
        },
        {
            "periodo": "1972-1973",
            "presidente": "Henrique S. Duque Estrada Meyer",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Atílio Benetti Sobrinho"
                },
                {
                    "cargo": "Diretor de Patrimônio",
                    "nome": "Benjamin David Sion"
                }
            ]
        },
        {
            "periodo": "1961-1962",
            "presidente": "Azarias Martins Villela",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Octavio Thyrso"
                },
                {
                    "cargo": "Diretor Tesoureiro",
                    "nome": "Gustavo Simon"
                }
            ]
        },
        {
            "periodo": "1933-1934",
            "presidente": "Galeno Gomes",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Júlio de Souza Avellar"
                }
            ]
        },
        {
            "periodo": "1932-1933",
            "presidente": "Armindo Cerqueira de S. Machado",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Pedro Orlando"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Manoel Thome do Nascimento"
                }
            ]
        },
        {
            "periodo": "1931-1932",
            "presidente": "José Mendes de Oliveira Castro",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Armindo Cerqueira de S. Machado"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Manoel Thome do Nascimento"
                }
            ]
        },
        {
            "periodo": "1928-1930",
            "presidente": "Octaviano Pinto Lopes Ribeiro",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Honório de Araújo Maia"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Júlio Vieira da Motta"
                }
            ]
        },
        {
            "periodo": "1927-1928",
            "presidente": "Galeno Gomes",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Christiano Heyn Haman"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Júlio Pedro de Fraga Lourenço"
                }
            ]
        },
        {
            "periodo": "1920-1926",
            "presidente": "Galeno Gomes",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "João Pedro de Fraga Lourenço"
                }
            ]
        },
        {
            "periodo": "1918-1919",
            "presidente": "Bernardo de Oliveira Barbosa",
            "diretores": [
                {
                    "cargo": "Tesoureiro",
                    "nome": "César Borges Palhares"
                }
            ]
        },
        {
            "periodo": "1916-1917",
            "presidente": "J. G. Pereira Lima",
            "diretores": [
                {
                    "cargo": "Tesoureiro",
                    "nome": "Honório de Araújo Maia"
                }
            ]
        },
        {
            "periodo": "1913-1915",
            "presidente": "José Luiz Ferreira Fontes",
            "diretores": [
                {
                    "cargo": "Tesoureiro",
                    "nome": "José Cândido Ferreira Moreira"
                }
            ]
        },
        {
            "periodo": "1908-1909",
            "presidente": "Antônio de Paula Rodigues Alves",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Alberto Augusto G. de Azevedo"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Carlos Leite Pinto"
                }
            ]
        },
        {
            "periodo": "1906-1907",
            "presidente": "Antônio da Silva Maia",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Honório de Araújo Maia"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Luiz Francisco Moreira"
                }
            ]
        },
        {
            "periodo": "1905-1906",
            "presidente": "José João Torres",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Arthur Ferreira M. Guimarães"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Adolpho Shimith"
                }
            ]
        },
        {
            "periodo": "1901-1904",
            "presidente": "Conde de Avelar",
            "diretores": [
                {
                    "cargo": "Secretário",
                    "nome": "Gustavo de Araújo Maia"
                },
                {
                    "cargo": "Tesoureiro",
                    "nome": "Antônio da Silva Maia"
                }
            ]
        }
    ],
    "atualizacao": "2025-10-13",
    "total_diretorias": 25

        };

        // Elementos da DOM
        this.btnVerDiretorias = document.getElementById('btn-ver-diretorias');
        this.secaoDiretoriaHistorica = document.getElementById('diretoria-historica');
        this.listaDiretorias = document.getElementById('lista-diretorias');
        this.filtroDecada = document.getElementById('filtro-decada');
        this.diretoresLista = document.getElementById('diretores-lista');
        this.presidenteNome = document.getElementById('presidente-nome');
        this.presidentePeriodo = document.getElementById('presidente-periodo');
        
        // Iniciar o gerenciador
        this.iniciar();
    }

    iniciar() {
        try {
            // Configurar eventos
            this.configurarEventos();
            
            // Exibir diretoria atual
            this.exibirDiretoriaAtual();
            
            // Preencher opções de década
            this.preencherDecadas();
            
        } catch (erro) {
            console.error('Erro ao carregar diretorias:', erro);
            this.exibirMensagemErro('Erro ao carregar as informações da diretoria. Por favor, tente novamente mais tarde.');
        }
    }

    configurarEventos() {
        // Botão para mostrar/ocultar diretorias antigas
        if (this.btnVerDiretorias) {
            this.btnVerDiretorias.addEventListener('click', () => {
                const estaVisivel = !this.secaoDiretoriaHistorica.classList.contains('hidden');
                
                if (estaVisivel) {
                    this.ocultarDiretoriasAntigas();
                } else {
                    this.mostrarDiretoriasAntigas();
                }
            });
        }

        // Filtro por década
        if (this.filtroDecada) {
            this.filtroDecada.addEventListener('change', (event) => {
                this.filtrarPorDecada(event.target.value);
            });
        }
    }

    exibirDiretoriaAtual() {
        if (!this.dadosDiretorias?.diretoria_atual) return;
        
        const { periodo, presidente, diretores } = this.dadosDiretorias.diretoria_atual;
        
        // Atualizar informações do presidente
        if (this.presidenteNome) this.presidenteNome.textContent = presidente || 'Não informado';
        if (this.presidentePeriodo) this.presidentePeriodo.textContent = `Período: ${periodo || 'Não informado'}`;
        
        // Limpar lista de diretores
        if (this.diretoresLista) {
            this.diretoresLista.innerHTML = '';
            
            // Adicionar cada diretor à lista
            if (diretores && diretores.length > 0) {
                diretores.forEach(diretor => {
                    this.adicionarDiretorCard(diretor);
                });
            } else {
                this.diretoresLista.innerHTML = '<p class="text-[#8B2635] text-center col-span-3">Nenhum diretor cadastrado.</p>';
            }
        }
    }

    adicionarDiretorCard(diretor) {
        if (!this.diretoresLista) return;
        
        const card = document.createElement('div');
        card.className = 'bg-gradient-to-br from-[#F5F0E8] to-white p-6 rounded-xl border border-[#F5F0E8] hover:border-[#8B2635] transition-colors';
        
        card.innerHTML = `
            <div class="flex items-start">
                <span class="material-icons text-[#8B2635] mr-3">person</span>
                <div>
                    <h5 class="font-bold text-[#6B4423] mb-1">${diretor.cargo || 'Cargo não informado'}</h5>
                    <p class="text-[#8B2635] font-medium">${diretor.nome || 'Nome não informado'}</p>
                </div>
            </div>
        `;
        
        this.diretoresLista.appendChild(card);
    }

    mostrarDiretoriasAntigas() {
        if (!this.secaoDiretoriaHistorica || !this.btnVerDiretorias) return;
        
        this.secaoDiretoriaHistorica.classList.remove('hidden');
        this.btnVerDiretorias.innerHTML = '<span>Ocultar Diretorias Anteriores</span><span class="ml-2 material-icons">expand_less</span>';
        
        // Se ainda não carregou as diretorias históricas, carrega agora
        if (this.listaDiretorias && this.listaDiretorias.children.length === 0) {
            this.exibirDiretoriasHistoricas();
        }
    }

    ocultarDiretoriasAntigas() {
        if (!this.secaoDiretoriaHistorica || !this.btnVerDiretorias) return;
        
        this.secaoDiretoriaHistorica.classList.add('hidden');
        this.btnVerDiretorias.innerHTML = '<span>Ver Diretorias Anteriores</span><span class="ml-2 material-icons">expand_more</span>';
    }

    exibirDiretoriasHistoricas() {
        if (!this.listaDiretorias) return;
        
        if (!this.dadosDiretorias?.diretorias_historicas?.length) {
            this.listaDiretorias.innerHTML = '<p class="text-[#8B2635] text-center">Nenhuma diretoria histórica encontrada.</p>';
            return;
        }
        
        // Limpar lista
        this.listaDiretorias.innerHTML = '';
        
        // Adicionar cada diretoria à lista
        this.dadosDiretorias.diretorias_historicas.forEach(diretoria => {
            this.adicionarDiretoriaHistorica(diretoria);
        });
    }

    adicionarDiretoriaHistorica(diretoria) {
        if (!this.listaDiretorias) return;
        
        const item = document.createElement('div');
        item.className = 'bg-white p-6 rounded-lg shadow border border-[#F5F0E8] hover:border-[#8B2635] transition-colors';
        
        // Extrair década para agrupamento
        const decada = this.extrairDecada(diretoria.periodo);
        
        item.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-xl font-bold text-[#6B4423]">${diretoria.periodo || 'Período não informado'}</h3>
                <span class="text-sm text-gray-500">${decada}s</span>
            </div>
            ${diretoria.presidente ? `
                <div class="mb-3">
                    <p class="font-medium text-[#8B2635]">Presidente</p>
                    <p class="text-[#6B4423]">${diretoria.presidente}</p>
                </div>
            ` : ''}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-4">
                ${diretoria.diretores && diretoria.diretores.length > 0 ? 
                    diretoria.diretores.map(diretor => `
                        <div>
                            <p class="font-medium text-[#8B2635] text-sm">${diretor.cargo || 'Cargo não informado'}</p>
                            <p class="text-[#6B4423]">${diretor.nome || 'Nome não informado'}</p>
                        </div>
                    `).join('') : 
                    '<p class="text-[#8B2635] text-sm">Nenhum diretor cadastrado para este período.</p>'
                }
            </div>
        `;
        
        // Adicionar atributo de dados para filtro
        item.setAttribute('data-decada', decada);
        
        this.listaDiretorias.appendChild(item);
    }

    preencherDecadas() {
        if (!this.filtroDecada || !this.dadosDiretorias?.diretorias_historicas?.length) return;
        
        // Extrair décadas únicas
        const decadas = new Set();
        
        this.dadosDiretorias.diretorias_historicas.forEach(diretoria => {
            const decada = this.extrairDecada(diretoria.periodo);
            if (decada) {
                decadas.add(decada);
            }
        });
        
        // Ordenar décadas em ordem decrescente
        const decadasOrdenadas = Array.from(decadas).sort((a, b) => b - a);
        
        // Limpar opções existentes
        this.filtroDecada.innerHTML = '<option value="todos">Todas as décadas</option>';
        
        // Adicionar opções ao select
        decadasOrdenadas.forEach(decada => {
            const option = document.createElement('option');
            option.value = decada;
            option.textContent = `${decada}s`;
            this.filtroDecada.appendChild(option);
        });
    }

    extrairDecada(periodo) {
        if (!periodo) return null;
        
        // Extrair o primeiro ano do período (assumindo formato "YYYY-YYYY" ou apenas "YYYY")
        const match = periodo.match(/^(\d{4})/);
        if (!match) return null;
        
        const ano = parseInt(match[1], 10);
        return Math.floor(ano / 10) * 10; // Retorna a década (ex: 2020 -> 2020)
    }

    filtrarPorDecada(decada) {
        if (!this.listaDiretorias) return;
        
        const itens = this.listaDiretorias.querySelectorAll('[data-decada]');
        
        itens.forEach(item => {
            if (decada === 'todos' || item.getAttribute('data-decada') === decada) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    exibirMensagemErro(mensagem) {
        console.error(mensagem);
        // Você pode adicionar aqui a lógica para exibir uma mensagem de erro na interface
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se estamos na página correta
    if (document.getElementById('tab-diretoria')) {
        new GerenciadorDiretorias();
    }
});
