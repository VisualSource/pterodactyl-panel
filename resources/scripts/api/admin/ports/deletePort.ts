import http from '@/api/http';

export default (id: number): Promise<void> => {
    return new Promise((ok,rej)=>{
        http.delete(`/api/application/ports/${id}`)
        .then(()=>ok()).catch(rej);
    });
}