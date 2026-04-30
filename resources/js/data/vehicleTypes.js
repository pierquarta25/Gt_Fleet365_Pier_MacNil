// Questa è la lista completa dei veicoli presi dal documento PDF
// Li dividiamo in categorie per replicare il layout a tabelle del documento originale

export const vehicleCategories = [
    {
        title: "Veicoli Stradali",
        icon: "fa-car",
        vehicles: [
            { id: "auto", name: "AUTO", img: "/media/AUTO.png" },
            { id: "auto_fringe", name: "AUTO (Fringe Benefit)", img: "/media/AUTO_Fringe_Benefit.png" },
            { id: "auto_elettrica", name: "AUTO ELETTRICA", img: "/media/AUTO_ELETTRICA.png" },
            { id: "auto_elettrica_fringe", name: "AUTO ELETTRICA (Fringe Benefit)", img: "/media/AUTO_ELETTRICA_Fringe_Benefit.png" },
            { id: "auto_ibrida", name: "AUTO IBRIDA", img: "/media/AUTO_IBRIDA.png" },
            { id: "auto_ibrida_fringe", name: "AUTO IBRIDA (Fringe Benefit)", img: "/media/AUTO_IBRIDA_Fringe_Benefit.png" },
            { id: "furgone", name: "FURGONE", img: "/media/FURGONE.png" },
            { id: "furgone_cassonato", name: "FURGONE CASSONATO", img: "/media/FURGONE_CASSONATO.png" },
            { id: "furgone_frigo", name: "FURGONE FRIGO", img: "/media/FURGONE_FRIGO.png" },
            { id: "furgone_multitemp", name: "FURGONE MULTI-TEMPERATURA", img: "/media/FURGONE_MULTITEMPERATURA.png" },
            { id: "moto", name: "MOTO", img: "/media/MOTO.png" },
            { id: "bus_alunni", name: "BUS TRASPORTO ALUNNI (scuola bus)", img: "/media/BUS_TRASPORTO_ALUNNI_scuola_bus.png" },
            { id: "bisarca", name: "BISARCA", img: "/media/BISARCA.png" },
            { id: "bus_privato", name: "BUS TRASPORTO PRIVATO", img: "/media/BUS_TRASPORTO_PRIVATO.png" },
            { id: "bus_pubblico", name: "BUS TRASPORTO PUBBLICO", img: "/media/BUS_TRASPORTO_PUBBLICO.png" },
            { id: "motrice_cisterna", name: "MOTRICE CISTERNA", img: "/media/MOTRICE_CISTERNA.png" },
            { id: "motrice_gru", name: "MOTRICE CON GRU", img: "/media/MOTRICE_CON_GRU.png" },
            { id: "motrice_frigo", name: "MOTRICE FRIGO", img: "/media/MOTRICE_FRIGO.png" },
            { id: "motrice_isotermica", name: "MOTRICE ISOTERMICA", img: "/media/MOTRICE_ISOTERMICA.png" },
        ]
    },
    {
        title: "Trasporto Pesante & Semirimorchi",
        icon: "fa-truck",
        vehicles: [
            { id: "motrice_container", name: "MOTRICE per CONTAINER", img: "/media/MOTRICE_per_CONTAINER.png" },
            { id: "motrice_rimorchio", name: "MOTRICE per RIMORCHIO", img: "/media/MOTRICE_per_RIMORCHIO.png" },
            { id: "golf_cart", name: "GOLF CART", img: "/media/GOLF_CART.png" },
            { id: "motrice_telonata", name: "MOTRICE TELONATA", img: "/media/MOTRICE_TELONATA.png" },
            { id: "spazzatrice", name: "SPAZZATRICE", img: "/media/SPAZZATRICE.png" },
            { id: "trattore_stradale", name: "TRATTORE STRADALE", img: "/media/TRATTORE_STRADALE.png" },
            { id: "veicolo_rifiuti", name: "VEICOLO RACCOLTA RIFIUTI", img: "/media/VEICOLO_RACCOLTA_RIFIUTI.png" },
            { id: "rimorchio_frigo", name: "RIMORCHIO FRIGO", img: "/media/RIMORCHIO_FRIGO.png" },
            { id: "rimorchio_motrici", name: "RIMORCHIO per MOTRICI", img: "/media/RIMORCHIO_per_MOTRICI.png" },
            { id: "semirimorchio", name: "SEMIRIMORCHIO", img: "/media/SEMIRIMORCHIO.png" },
            { id: "semirimorchio_frigo", name: "SEMIRIMORCHIO FRIGO", img: "/media/SEMIRIMORCHIO_FRIGO.png" },
            { id: "semirimorchio_isotermico", name: "SEMIRIMORCHIO ISOTERMICO", img: "/media/SEMIRIMORCHIO_ISOTERMICO.png" },
            { id: "semirimorchio_telonato", name: "SEMIRIMORCHIO TELONATO", img: "/media/SEMIRIMORCHIO_TELONATO.png" },
            { id: "semirimorchio_container", name: "SEMIRMORCHIO per CONTAINER o TRASPORTO COSE", img: "/media/SEMIRIMORCHIO_per_CONTAINER_o_TRASPORTO_COSE.png" },
            { id: "attrezzatura_cantiere", name: "ATTREZZATURA DA CANTIERE", img: "/media/ATTREZZATURA_DA_CANTIERE.png" },
            { id: "bagno_chimico", name: "BAGNO CHIMICO", img: "/media/BAGNO_CHIMICO.png" },
            { id: "cassa_mobile", name: "CASSA MOBILE", img: "/media/CASSA_MOBILE.png" },
            { id: "cassone_scarrabile", name: "CASSONE SCARRABILE", img: "/media/CASSONE_SCARRABILE.png" },
            { id: "container", name: "CONTAINER", img: "/media/CONTAINER.png" },
        ]
    },
    {
        title: "Asset, Cantieri & Speciali",
        icon: "fa-tools",
        vehicles: [
            { id: "cucina_mobile", name: "CUCINA MOBILE", img: "/media/CUCINA_MOBILE.png" },
            { id: "gommone", name: "GOMMONE", img: "/media/GOMMONE.png" },
            { id: "gruppo_elettrogeno", name: "GRUPPO ELETTROGENO", img: "/media/GRUPPO_ELETTROGENO.png" },
            { id: "betoniera", name: "BETONIERA", img: "/media/BETONIERA.png" },
            { id: "mezzi_cantiere", name: "MACCHINARI PER CANTIERI EDILI", img: "/media/MACCHINARI_PER_CANTIERI_EDILI.png" },
            { id: "mezzi_magazzino", name: "MEZZI DA MAGAZZINO", img: "/media/MEZZI_DA_MAGAZZINO.png" },
            { id: "mezzi_movimento_terra", name: "MEZZI MOVIMENTO TERRA", img: "/media/MEZZI_MOVIMENTO_TERRA.png" },
            { id: "mezzi_cave", name: "MEZZI PER CAVE", img: "/media/MEZZI_PER_CAVE.png" },
            { id: "mezzo_portuale", name: "MEZZO PORTUALE", img: "/media/MEZZO_PORTUALE.png" },
            { id: "minidumper", name: "MINIDUMPER", img: "/media/MINIDUMPER.png" },
            { id: "motocarriola", name: "MOTOCARRIOLA", img: "/media/MOTOCARRIOLA.png" },
            { id: "muletto", name: "MULETTO", img: "/media/MULETTO.png" },
            { id: "piattaforma_furgone", name: "PIATTAFORMA AEREA (su Furgone)", img: "/media/PIATTAFORMA_AEREA_su_Furgone.png" },
            { id: "piattaforma_gomma", name: "PIATTAFORME AEREE (su Gomma, Cingoli)", img: "/media/PIATTAFORME_AEREE_su_Gomma_Cingoli.png" },
            { id: "trattore_agricolo", name: "TRATTORE AGRICOLO", img: "/media/TRATTORE_AGRICOLO.png" },
            { id: "trattore_compatto", name: "TRATTORE AGRICOLO COMPATTO", img: "/media/TRATTORE_AGRICOLO_COMPATTO.png" },
        ]
    }
];
