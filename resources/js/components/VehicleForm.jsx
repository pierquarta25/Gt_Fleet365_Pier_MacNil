import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { vehicleCategories } from '../data/vehicleTypes';

export default function VehicleForm() {
    const [step, setStep] = useState(1);
    const [errors, setErrors] = useState({}); // Traccio gli errori dei singoli campi
    const [loading, setLoading] = useState(false); // Stato per il caricamento finale
    
    // Inizializzo i dati prendendoli dal localStorage se esistono, altrimenti vuoti
    const [clientData, setClientData] = useState(() => {
        const saved = localStorage.getItem('gtfleet_clientData');
        return saved ? JSON.parse(saved) : {
            company: '', contact: '', email: '', phone: '', notes: '',
            italia: false, estero: false
        };
    });

    const [quantities, setQuantities] = useState(() => {
        const saved = localStorage.getItem('gtfleet_quantities');
        return saved ? JSON.parse(saved) : {};
    });

    // Salvo i dati nel localStorage ogni volta che cambiano
    useEffect(() => {
        localStorage.setItem('gtfleet_clientData', JSON.stringify(clientData));
    }, [clientData]);

    useEffect(() => {
        localStorage.setItem('gtfleet_quantities', JSON.stringify(quantities));
    }, [quantities]);

    // Gestisco il cambio dei testi e resetto l'errore del campo che sto scrivendo
    const handleTextChange = (e) => {
        const { id, value } = e.target;
        setClientData({ ...clientData, [id]: value });
        
        // Se c'era un errore su questo campo, lo cancello mentre l'utente digita
        if (errors[id]) {
            setErrors({ ...errors, [id]: null });
        }
    };

    // Funzione per validare il primo passo
    const validateStep1 = () => {
        let newErrors = {};
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!clientData.company) newErrors.company = "La ragione sociale è obbligatoria";
        if (!clientData.contact) newErrors.contact = "Il nome contatto è obbligatorio";
        
        if (!clientData.email) {
            newErrors.email = "L'email è obbligatoria";
        } else if (!emailRegex.test(clientData.email)) {
            newErrors.email = "Inserisci un indirizzo email valido";
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const togglePlace = (place) => {
        setClientData({ ...clientData, [place]: !clientData[place] });
    };

    const handleQtyChange = (id, value) => {
        setQuantities({ ...quantities, [id]: parseInt(value) || 0 });
    };

    // Invio finale dei dati
    const handleSubmit = async () => {
        if (loading) return; // Evito invii multipli se sta già caricando
        
        setLoading(true);
        try {
            await axios.post('/api/vehicle-form', { client: clientData, vehicles: quantities });
            
            // Se l'invio ha successo, pulisco il localStorage per il prossimo giro
            localStorage.removeItem('gtfleet_clientData');
            localStorage.removeItem('gtfleet_quantities');
            
            alert('Perfetto! I tuoi dati sono stati inviati a HubSpot con successo.');
            window.location.reload(); // Ricarico per resettare tutto
        } catch (error) {
            console.error(error);
            alert('Ops! Qualcosa è andato storto durante l\'invio. Riprova tra poco.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="app-main">
            
            {/* HEADER */}
            <header>
                <div className="header-inner">
                    <div className="logo-area">
                        <img src="/media/logo.png" alt="Logo" className="logo-img" />
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

            <div className="container">

                {step === 1 && (
                    <div className="fade-in">
                        {/* HERO BANNER CON ICONA ORIGINALE */}
                        <div className="step1-hero">
                            <div className="step1-hero-icon">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="3" />
                                    <path d="M7 8h10M7 12h7M7 16h5" />
                                </svg>
                            </div>
                            <div className="hero-text">
                                <h1>Configurazione Flotta</h1>
                                <p>Inserisci i dati aziendali del cliente e seleziona la tipologia e la quantità dei mezzi da monitorare.</p>
                            </div>
                            <div className="btn-new">Nuovo Preventivo</div>
                        </div>

                        <div className="card">
                            <div className="card-header">
                                <i className="fas fa-list-alt" style={{color:'white'}}></i>
                                <h2>Dati Cliente</h2>
                            </div>
                            
                            {Object.keys(errors).length > 0 && (
                                <div className="global-error" style={{margin: '20px 22px 0 22px'}}>
                                    ⚠️ Attenzione: controlla i campi evidenziati.
                                </div>
                            )}

                            <div className="cf-section">
                                <div className="cf-section-label"><span>🏢</span><span>Azienda</span></div>
                                <div className="cf-fields">
                                    <div className="field-group">
                                        <label>Nome Azienda *</label>
                                        <input 
                                            type="text" 
                                            id="company" 
                                            className={errors.company ? 'input-error' : ''}
                                            value={clientData.company} 
                                            onChange={handleTextChange} 
                                            placeholder="es. Logistica Sud S.r.l." 
                                        />
                                        {errors.company && <span className="error-text">{errors.company}</span>}
                                    </div>
                                </div>
                            </div>

                            <div className="cf-section">
                                <div className="cf-section-label"><span>👤</span><span>Referente</span></div>
                                <div className="cf-fields row-layout">
                                    <div className="field-group flex-1">
                                        <label>Nome Contatto *</label>
                                        <input 
                                            type="text" 
                                            id="contact" 
                                            className={errors.contact ? 'input-error' : ''}
                                            value={clientData.contact} 
                                            onChange={handleTextChange} 
                                            placeholder="es. Mario Rossi" 
                                        />
                                        {errors.contact && <span className="error-text">{errors.contact}</span>}
                                    </div>
                                    <div className="field-group flex-1">
                                        <label>Email *</label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            className={errors.email ? 'input-error' : ''}
                                            value={clientData.email} 
                                            onChange={handleTextChange} 
                                            placeholder="es. mario@email.it" 
                                        />
                                        {errors.email && <span className="error-text">{errors.email}</span>}
                                    </div>
                                    <div className="field-group flex-1">
                                        <label>Telefono</label>
                                        <input 
                                            type="text" 
                                            id="phone" 
                                            value={clientData.phone} 
                                            onChange={handleTextChange} 
                                            placeholder="es. +39..." 
                                        />
                                    </div>
                                </div>
                            </div>

                            <div className="cf-section">
                                <div className="cf-section-label"><span>📡</span><span>Servizi</span></div>
                                <div className="cf-fields row-layout">
                                    <div className="field-group flex-2">
                                        <label>Note</label>
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
                                        <div className="toggle-group">
                                            <button className={`toggle-btn ${clientData.italia ? 'active' : ''}`} onClick={() => togglePlace('italia')}>🇮🇹 Italia</button>
                                            <button className={`toggle-btn ${clientData.estero ? 'active' : ''}`} onClick={() => togglePlace('estero')}>🌍 Estero</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bottom-bar">
                            <span className="step-info">Passo <strong>1</strong> di 3</span>
                            <button className="btn btn-primary" onClick={() => {
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
                                                <td className="text-center"><img src={v.img} height="38" alt="" /></td>
                                                <td className="text-center">
                                                    <input 
                                                        type="number" 
                                                        className={`qty-input ${quantities[v.id] > 0 ? 'filled' : ''}`} 
                                                        value={quantities[v.id] || ''} 
                                                        onChange={(e) => handleQtyChange(v.id, e.target.value)} 
                                                        placeholder="0"
                                                    />
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        ))}
                        <div className="bottom-bar">
                            <div className="total-info">Totale: <strong>{Object.values(quantities).reduce((a, b) => a + b, 0)}</strong> mezzi</div>
                            <div style={{display: 'flex', gap: '10px'}}>
                                <button className="btn btn-ghost" onClick={() => setStep(1)}>← Indietro</button>
                                <button className="btn btn-primary" onClick={() => setStep(3)}>Avanti: Riepilogo →</button>
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
                                    <div className="summary-item"><small>CONTATTO</small><strong>{clientData.contact}</strong></div>
                                    <div className="summary-item"><small>EMAIL</small><strong>{clientData.email}</strong></div>
                                    <div className="summary-item"><small>TELEFONO</small><strong>{clientData.phone || '—'}</strong></div>
                                    <div className="summary-item"><small>TRAFFICO 4G</small><strong>{[clientData.italia ? 'Italia' : '', clientData.estero ? 'Estero' : ''].filter(Boolean).join(', ') || '—'}</strong></div>
                                    <div className="summary-item"><small>NOTE</small><strong>{clientData.notes || '—'}</strong></div>
                                </div>
                                <p className="section-title">Mezzi selezionati:</p>
                                <div className="summary-grid">
                                    {Object.entries(quantities).map(([id, q]) => {
                                        if (q <= 0) return null;
                                        const mezzo = vehicleCategories.flatMap(c => c.vehicles).find(v => v.id === id);
                                        return (
                                            <div className="summary-card" key={id}>
                                                <img src={mezzo.img} alt="" />
                                                <span className="mezzo-title">{mezzo.name}</span>
                                                <span className="mezzo-qty">{q}</span>
                                            </div>
                                        )
                                    })}
                                </div>
                                <div className="total-badge-container">
                                    <div className="final-total-badge">
                                        TOTALE: {Object.values(quantities).reduce((a, b) => a + b, 0)} MEZZI
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="bottom-bar">
                            <span className="step-info"></span>
                            <div className="btn-group" style={{display: 'flex', gap: '10px'}}>
                                <button className="btn btn-ghost" onClick={() => setStep(2)}>← Modifica Mezzi</button>
                                <button 
                                    className="btn btn-accent" 
                                    onClick={handleSubmit} 
                                    disabled={loading}
                                    style={{minWidth: '160px', justifyContent: 'center'}}
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
            </div>
        </div>
    );
}
