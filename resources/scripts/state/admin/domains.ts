import { action, type Action } from 'easy-peasy';

export interface AdminDomainStore {
    selectedDomains: number[];

    setSelectedDomains: Action<AdminDomainStore, number[]>;
    appendSelectedDomains: Action<AdminDomainStore, number>;
    removeSelectedDomains: Action<AdminDomainStore, number>;
}

const domains: AdminDomainStore = {
    selectedDomains: [],

    setSelectedDomains: action((state,payload) => {
        state.selectedDomains = payload;
    }),

    appendSelectedDomains: action((state,payload)=>{
        state.selectedDomains = state.selectedDomains.filter(id => id !== payload).concat(payload);
    }),

    removeSelectedDomains: action((state,payload)=>{
        state.selectedDomains = state.selectedDomains.filter(id => id !== payload);
    })
}

export default domains;