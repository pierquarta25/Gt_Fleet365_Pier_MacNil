import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { vehicleCategories } from '../data/vehicleTypes';

export default function VehicleForm() {
    const [step, setStep] = useState(1);
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [modalConfig, setModalConfig] = useState({
        isOpen: false,
        type: 'success',
        title: '',
        message: ''
    });

    // Inizializza i dati dal localStorage o usa i valori predefiniti
    const [clientData, setClientData] = useState(() => {
        const saved = localStorage.getItem('gtfleet_clientData');

        // Estrae lo slug dell'agente se presente nella query dell'URL (?agent=slug)
        const params = new URLSearchParams(window.location.search);
        const agentSlug = params.get('agent') || '';

        // Estrae l'email dell'agente se presente nel percorso dell'URL
        const path = window.location.pathname.replace(/^\//, '');
        const agentEmail = path.includes('@') ? path : '';

        const defaultData = {
            company: '',
            contact: '',
            lastname: '',
            email: '',
            phone: '',
            drivers: 0,
            notes: '',
            italia: false,
            estero: false,
            agent_email: agentEmail,
            agent_slug: agentSlug
        };

        if (saved) {
            const parsed = JSON.parse(saved);
            // Mantiene l'email e lo slug dell'agente aggiornati dall'URL
            return {
                lastname: '',
                drivers: 0,
                ...parsed,
                agent_email: agentEmail || parsed.agent_email || '',
                agent_slug: agentSlug || parsed.agent_slug || ''
            };
        }
        return defaultData;
    });

    // Inizializza le quantità dal localStorage
    const [quantities, setQuantities] = useState(() => {
        const saved = localStorage.getItem('gtfleet_quantities');
        return saved ? JSON.parse(saved) : {};
    });

    // Salva i dati cliente nel localStorage ad ogni modifica
    useEffect(() => {
        localStorage.setItem('gtfleet_clientData', JSON.stringify(clientData));
    }, [clientData]);

    // Salva le quantità nel localStorage ad ogni modifica
    useEffect(() => {
        localStorage.setItem('gtfleet_quantities', JSON.stringify(quantities));
    }, [quantities]);

    // Gestisce le modifiche ai campi di testo e rimuove l'errore relativo
    const handleTextChange = (e) => {
        const { id, value } = e.target;
        setClientData(prev => ({ ...prev, [id]: value }));

        if (errors[id]) {
            setErrors(prev => ({ ...prev, [id]: null }));
        }
    };

    // Valida i campi obbligatori dello Step 1
    const validateStep1 = () => {
        const newErrors = {};
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!clientData.company.trim()) {
            newErrors.company = "La ragione sociale è obbligatoria";
        }
        if (!clientData.contact.trim()) {
            newErrors.contact = "Il nome contatto è obbligatorio";
        }
        if (!clientData.lastname || !clientData.lastname.trim()) {
            newErrors.lastname = "Il cognome contatto è obbligatorio";
        }
        if (!clientData.email.trim()) {
            newErrors.email = "L'email è obbligatoria";
        } else if (!emailRegex.test(clientData.email.trim())) {
            newErrors.email = "Inserisci un indirizzo email valido";
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    // Attiva/disattiva le opzioni di traffico dati
    const togglePlace = (place) => {
        setClientData(prev => ({ ...prev, [place]: !prev[place] }));
    };

    // Aggiorna la quantità di un veicolo specifico
    const handleQtyChange = (id, value) => {
        setQuantities(prev => ({ ...prev, [id]: parseInt(value, 10) || 0 }));
    };

    // Invia i dati al backend
    const handleSubmit = async () => {
        if (loading) return;

        setLoading(true);
        try {
            // Estrae i dettagli completi dei soli veicoli selezionati con quantità > 0
            const selectedVehiclesDetails = Object.entries(quantities)
                .filter(([_, qty]) => qty > 0)
                .map(([id, qty]) => {
                    const mezzo = vehicleCategories.flatMap(c => c.vehicles).find(v => v.id === id);
                    return {
                        id: id,
                        name: mezzo ? mezzo.name : id,
                        img: mezzo ? mezzo.img : '',
                        qty: qty
                    };
                });

            await axios.post('/api/vehicle-form', {
                client: clientData,
                vehicles: quantities,
                selectedVehicles: selectedVehiclesDetails
            });

            // Pulisce il localStorage dopo l'invio con successo
            localStorage.removeItem('gtfleet_clientData');
            localStorage.removeItem('gtfleet_quantities');

            setModalConfig({
                isOpen: true,
                type: 'success',
                title: 'Configurazione Inviata!',
                message: 'Perfetto! I tuoi dati sono stati elaborati e inviati con successo.'
            });

            // Chiude la modale e ricarica la pagina automaticamente dopo 3 secondi
            setTimeout(() => {
                setModalConfig(prev => ({ ...prev, isOpen: false }));
                window.location.reload();
            }, 3000);
        } catch (error) {
            console.error('Errore durante l\'invio:', error);
            setModalConfig({
                isOpen: true,
                type: 'error',
                title: 'Ops! Qualcosa è andato storto',
                message: 'Non è stato possibile inviare i dati. Controlla la connessione e riprova.'
            });
        } finally {
            setLoading(false);
        }
    };

    const totalVehicles = Object.values(quantities).reduce((acc, qty) => acc + qty, 0);

    return (
        <div className="app-main">
            {/* HEADER */}
            <header>
                <div className="header-inner">
                    <div className="logo-area">
                        <img src="/media/logo.png" alt="GT Fleet 365 Logo" className="logo-img" />
                    </div>

                    <div className="step-badges">
                        <div className="step-item">
                            <div className={`step-badge ${step >= 1 ? 'active' : ''} ${step > 1 ? 'done' : ''}`}>
                                {step > 1 ? '✓' : '1'}
                            </div>
                            <span className="step-label">Dati Cliente</span>
                        </div>
                        <div className={`step-line ${step > 1 ? 'done' : ''}`}></div>

                        <div className="step-item">
                            <div className={`step-badge ${step === 2 ? 'active' : step > 2 ? 'done' : 'pending'}`}>
                                {step > 2 ? '✓' : '2'}
                            </div>
                            <span className="step-label">Tipologia Mezzi</span>
                        </div>
                        <div className={`step-line ${step > 2 ? 'done' : ''}`}></div>

                        <div className="step-item">
                            <div className={`step-badge ${step === 3 ? 'active' : 'pending'}`}>3</div>
                            <span className="step-label">Riepilogo</span>
                        </div>
                    </div>
                </div>
            </header>

            <main className="container">
                <h1 className="visually-hidden">GT Fleet 365 - Configurazione Flotta</h1>

                {step === 1 && (
                    <div className="fade-in">
                        {/* HERO BANNER */}
                        <div className="step1-hero">
                            <div className="step1-hero-icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="3" />
                                    <path d="M7 8h10M7 12h7M7 16h5" />
                                </svg>
                            </div>
                            <div className="hero-text">
                                <h2>Configurazione Flotta</h2>
                                <p>Inserisci i dati aziendali del cliente e seleziona la tipologia e la quantità dei mezzi da monitorare.</p>
                            </div>
                            <div className="btn-new">Nuovo Preventivo</div>
                        </div>

                        <div className="card">
                            <div className="card-header">
                                <i className="fas fa-list-alt" style={{ color: 'white' }}></i>
                                <h2>Dati Cliente</h2>
                            </div>

                            {Object.keys(errors).length > 0 && (
                                <div className="global-error" style={{ margin: '20px 22px 0 22px' }} role="alert">
                                    ⚠️ Attenzione: controlla i campi evidenziati.
                                </div>
                            )}

                            <div className="cf-section">
                                <div className="cf-section-label"><span>🏢</span><span>Azienda</span></div>
                                <div className="cf-fields">
                                    <div className="field-group">
                                        <label htmlFor="company">Nome Azienda *</label>
                                        <input
                                            type="text"
                                            id="company"
                                            className={errors.company ? 'input-error' : ''}
                                            value={clientData.company}
                                            onChange={handleTextChange}
                                            placeholder="es. Logistica Sud S.r.l."
                                            aria-required="true"
                                            aria-invalid={errors.company ? "true" : "false"}
                                            aria-describedby={errors.company ? "company-error" : undefined}
                                        />
                                        {errors.company && <span id="company-error" className="error-text">{errors.company}</span>}
                                    </div>
                                </div>
                            </div>

                            <div className="cf-section">
                                <div className="cf-section-label"><span>👤</span><span>Referente</span></div>
                                <div className="cf-fields row-layout">
                                    <div className="field-group flex-1">
                                        <label htmlFor="contact">Nome Contatto *</label>
                                        <input
                                            type="text"
                                            id="contact"
                                            className={errors.contact ? 'input-error' : ''}
                                            value={clientData.contact}
                                            onChange={handleTextChange}
                                            placeholder="es. Mario"
                                            aria-required="true"
                                            aria-invalid={errors.contact ? "true" : "false"}
                                            aria-describedby={errors.contact ? "contact-error" : undefined}
                                        />
                                        {errors.contact && <span id="contact-error" className="error-text">{errors.contact}</span>}
                                    </div>
                                    <div className="field-group flex-1">
                                        <label htmlFor="lastname">Cognome Contatto *</label>
                                        <input
                                            type="text"
                                            id="lastname"
                                            className={errors.lastname ? 'input-error' : ''}
                                            value={clientData.lastname || ''}
                                            onChange={handleTextChange}
                                            placeholder="es. Rossi"
                                            aria-required="true"
                                            aria-invalid={errors.lastname ? "true" : "false"}
                                            aria-describedby={errors.lastname ? "lastname-error" : undefined}
                                        />
                                        {errors.lastname && <span id="lastname-error" className="error-text">{errors.lastname}</span>}
                                    </div>
                                    <div className="field-group flex-1">
                                        <label htmlFor="email">Email *</label>
                                        <input
                                            type="email"
                                            id="email"
                                            className={errors.email ? 'input-error' : ''}
                                            value={clientData.email}
                                            onChange={handleTextChange}
                                            placeholder="es. mario@email.it"
                                            aria-required="true"
                                            aria-invalid={errors.email ? "true" : "false"}
                                            aria-describedby={errors.email ? "email-error" : undefined}
                                        />
                                        {errors.email && <span id="email-error" className="error-text">{errors.email}</span>}
                                    </div>
                                    <div className="field-group flex-1">
                                        <label htmlFor="phone">Telefono</label>
                                        <input
                                            type="tel"
                                            id="phone"
                                            value={clientData.phone}
                                            onChange={handleTextChange}
                                            placeholder="es. +39..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="cf-section">
                                <div className="cf-section-label"><span>👥</span><span>Autisti</span></div>
                                <div className="cf-fields row-layout">
                                    <div className="field-group" style={{ marginBottom: 0 }}>
                                        <label htmlFor="drivers">Numero Autisti</label>
                                        <div className="counter-container">
                                            <button 
                                                type="button" 
                                                className="counter-btn" 
                                                onClick={() => setClientData(prev => ({ ...prev, drivers: Math.max(0, (prev.drivers || 0) - 1) }))}
                                                aria-label="Diminuisci autisti"
                                            >
                                                -
                                            </button>
                                            <input
                                                type="number"
                                                id="drivers"
                                                className="counter-input"
                                                value={clientData.drivers || 0}
                                                onChange={(e) => {
                                                    const val = parseInt(e.target.value, 10);
                                                    setClientData(prev => ({ ...prev, drivers: isNaN(val) ? 0 : Math.max(0, val) }));
                                                }}
                                                min="0"
                                                aria-label="Numero Autisti"
                                            />
                                            <button 
                                                type="button" 
                                                className="counter-btn" 
                                                onClick={() => setClientData(prev => ({ ...prev, drivers: (prev.drivers || 0) + 1 }))}
                                                aria-label="Aumenta autisti"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="cf-section">
                                <div className="cf-section-label"><span>📡</span><span>Servizi</span></div>
                                <div className="cf-fields row-layout">
                                    <div className="field-group flex-2">
                                        <label htmlFor="notes">Note</label>
                                        <input
                                            type="text"
                                            id="notes"
                                            value={clientData.notes}
                                            onChange={handleTextChange}
                                            placeholder="es. Flotta principalmente al Nord Italia"
                                        />
                                    </div>
                                    <div className="field-group flex-1">
                                        <label>Traffico dati 4G</label>
                                        <div className="toggle-group" role="group" aria-label="Ambito di traffico dati 4G">
                                            <button type="button" className={`toggle-btn ${clientData.italia ? 'active' : ''}`} onClick={() => togglePlace('italia')} aria-pressed={clientData.italia}>🇮🇹 Italia</button>
                                            <button type="button" className={`toggle-btn ${clientData.estero ? 'active' : ''}`} onClick={() => togglePlace('estero')} aria-pressed={clientData.estero}>🌍 Estero</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bottom-bar">
                            <span className="step-info">Passo <strong>1</strong> di 3</span>
                            <button type="button" className="btn btn-primary" onClick={() => {
                                if (validateStep1()) {
                                    setStep(2);
                                }
                            }}>Avanti: Inserisci Mezzi →</button>
                        </div>
                    </div>
                )}

                {step === 2 && (
                    <div className="fade-in">
                        {vehicleCategories.map((cat, i) => (
                            <div className="card" key={i}>
                                <div className="card-header">
                                    <h2>{cat.title}</h2>
                                </div>
                                <table className="vehicle-table">
                                    <thead>
                                        <tr>
                                            <th>Tipo Mezzo</th>
                                            <th className="text-center">Immagine</th>
                                            <th className="text-center">Quantità Mezzi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {cat.vehicles.map(v => (
                                            <tr key={v.id}>
                                                <td className="mezzo-name">{v.name}</td>
                                                <td className="text-center"><img src={v.img} height="38" alt={`Immagine ${v.name}`} /></td>
                                                <td className="text-center">
                                                    <input
                                                        type="number"
                                                        id={`qty-${v.id}`}
                                                        name={`qty-${v.id}`}
                                                        className={`qty-input ${quantities[v.id] > 0 ? 'filled' : ''}`}
                                                        value={quantities[v.id] || ''}
                                                        onChange={(e) => handleQtyChange(v.id, e.target.value)}
                                                        placeholder="0"
                                                        min="0"
                                                        aria-label={`Quantità per ${v.name}`}
                                                    />
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ))}
                        <div className="bottom-bar">
                            <div className="total-info">Totale: <strong>{totalVehicles}</strong> mezzi</div>
                            <div style={{ display: 'flex', gap: '10px' }}>
                                <button type="button" className="btn btn-ghost" onClick={() => setStep(1)}>← Indietro</button>
                                <button type="button" className="btn btn-primary" onClick={() => setStep(3)}>Avanti: Riepilogo →</button>
                            </div>
                        </div>
                    </div>
                )}

                {step === 3 && (
                    <div className="fade-in">
                        <div className="card">
                            <div className="card-header"><h2>Riepilogo finale</h2></div>
                            <div className="card-body-padding">
                                <div className="client-summary">
                                    <div className="summary-item"><small>AZIENDA</small><strong>{clientData.company}</strong></div>
                                    <div className="summary-item"><small>CONTATTO</small><strong>{clientData.contact} {clientData.lastname || ''}</strong></div>
                                    <div className="summary-item"><small>EMAIL</small><strong>{clientData.email}</strong></div>
                                    <div className="summary-item"><small>TELEFONO</small><strong>{clientData.phone || '—'}</strong></div>
                                    <div className="summary-item"><small>AUTISTI</small><strong>{clientData.drivers || 0}</strong></div>
                                    <div className="summary-item"><small>TRAFFICO 4G</small><strong>{[clientData.italia ? 'Italia' : '', clientData.estero ? 'Estero' : ''].filter(Boolean).join(', ') || '—'}</strong></div>
                                    <div className="summary-item"><small>NOTE</small><strong>{clientData.notes || '—'}</strong></div>
                                </div>
                                <p className="section-title">Mezzi selezionati:</p>
                                <div className="summary-grid">
                                    {Object.entries(quantities).map(([id, q]) => {
                                        if (q <= 0) return null;
                                        const mezzo = vehicleCategories.flatMap(c => c.vehicles).find(v => v.id === id);
                                        if (!mezzo) return null;
                                        return (
                                            <div className="summary-card" key={id}>
                                                <img src={mezzo.img} alt={`Immagine ${mezzo.name}`} />
                                                <span className="mezzo-title">{mezzo.name}</span>
                                                <span className="mezzo-qty">{q}</span>
                                            </div>
                                        )
                                    })}
                                </div>
                                <div className="total-badge-container">
                                    <div className="final-total-badge">
                                        TOTALE: {totalVehicles} MEZZI
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bottom-bar">
                            <span className="step-info"></span>
                            <div className="btn-group" style={{ display: 'flex', gap: '10px' }}>
                                <button type="button" className="btn btn-ghost" onClick={() => setStep(2)}>← Modifica Mezzi</button>
                                <button
                                    type="button"
                                    className="btn btn-accent"
                                    onClick={handleSubmit}
                                    disabled={loading}
                                    style={{ minWidth: '160px', justifyContent: 'center' }}
                                >
                                    {loading ? (
                                        <>
                                            <div className="spinner"></div>
                                            Invio in corso...
                                        </>
                                    ) : (
                                        <>Conferma e Invia</>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                )}
            </main>

            {modalConfig.isOpen && (
                <div className="modal-overlay fade-in">
                    <div className="modal-custom">
                        <div className={`modal-icon ${modalConfig.type}`}>
                            {modalConfig.type === 'success' ? (
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            ) : (
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18" />
                                    <line x1="6" y1="6" x2="18" y2="18" />
                                </svg>
                            )}
                        </div>
                        <h3 className="modal-title">{modalConfig.title}</h3>
                        <p className="modal-message">{modalConfig.message}</p>
                        <button
                            type="button"
                            className="btn btn-primary modal-btn"
                            onClick={() => {
                                setModalConfig(prev => ({ ...prev, isOpen: false }));
                                if (modalConfig.type === 'success') {
                                    window.location.reload();
                                }
                            }}
                        >
                            {modalConfig.type === 'success' ? 'Nuova Configurazione' : 'Riprova'}
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
