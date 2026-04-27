import React, { useState } from 'react';
import { Lock, LogIn, ShieldCheck, AlertCircle } from 'lucide-react';
import { motion } from 'framer-motion';
import { TIMEOUTS } from '../lib/constants';

export const LoginPage = ({ onLogin }) => {
    const [token, setToken] = useState('');
    const [error, setError] = useState('');
    const [isLoading, setIsLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsLoading(true);
        setError('');

        // For a "Simple Admin Auth", we just check against the hardcoded token
        // In a real app, this would be a POST /api/login that returns a JWT
        if (token === 'escoffie_secret_2026') {
            setTimeout(() => {
                localStorage.setItem('admin_token', token);
                onLogin();
            }, TIMEOUTS.AUTH_SIMULATION_MS);
        } else {
            setTimeout(() => {
                setError('Invalid administrator token.');
                setIsLoading(false);
            }, TIMEOUTS.AUTH_SIMULATION_MS / 2);
        }
    };

    return (
        <div className="min-h-screen bg-slate-950 flex flex-col items-center justify-center p-4 relative overflow-hidden">
            {/* Background Decorations */}
            <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div className="absolute -top-24 -left-24 w-96 h-96 bg-brand-primary/10 rounded-full blur-3xl opacity-50" />
                <div className="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-secondary/10 rounded-full blur-3xl opacity-50" />
            </div>

            <motion.div 
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="w-full max-w-md"
            >
                <div className="text-center mb-8">
                    <div className="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-primary/10 border border-brand-primary/20 mb-4">
                        <ShieldCheck className="text-brand-primary" size={32} />
                    </div>
                    <h1 className="text-3xl font-black text-white tracking-tight mb-2">
                        Escoffie<span className="text-brand-primary">News</span>
                    </h1>
                    <p className="text-slate-400 font-medium">Administrator Access Required</p>
                </div>

                <div className="bg-slate-900/50 backdrop-blur-xl border border-slate-800 p-8 rounded-3xl shadow-2xl">
                    <form onSubmit={handleSubmit} className="space-y-6">
                        <div>
                            <label className="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-2 ml-1">
                                Admin Secret Token
                            </label>
                            <div className="relative group">
                                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <Lock className="text-slate-500 group-focus-within:text-brand-primary transition-colors" size={18} />
                                </div>
                                <input
                                    type="password"
                                    value={token}
                                    onChange={(e) => setToken(e.target.value)}
                                    placeholder="Enter your security token..."
                                    className="block w-full pl-12 pr-4 py-4 bg-slate-800/50 border border-slate-700 rounded-2xl text-white placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-brand-primary/50 focus:border-brand-primary transition-all font-mono"
                                    required
                                />
                            </div>
                        </div>

                        {error && (
                            <motion.div 
                                initial={{ opacity: 0, x: -10 }}
                                animate={{ opacity: 1, x: 0 }}
                                className="flex items-center gap-2 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm font-medium"
                            >
                                <AlertCircle size={16} />
                                {error}
                            </motion.div>
                        )}

                        <button
                            type="submit"
                            disabled={isLoading}
                            className="w-full py-4 bg-brand-primary hover:bg-brand-primary/90 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold rounded-2xl shadow-lg shadow-brand-primary/20 flex items-center justify-center gap-2 transition-all active:scale-[0.98]"
                        >
                            {isLoading ? (
                                <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                            ) : (
                                <>
                                    <LogIn size={20} />
                                    Access Dashboard
                                </>
                            )}
                        </button>
                    </form>
                </div>

                <div className="mt-8 text-center">
                    <p className="text-slate-600 text-[10px] uppercase tracking-widest font-bold">
                        Your access token is defined in your <code className="text-slate-500">.env</code> file
                    </p>
                </div>
            </motion.div>
        </div>
    );
};
