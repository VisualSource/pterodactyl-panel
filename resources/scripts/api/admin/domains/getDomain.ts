import http from '@/api/http';
import { type Domain, rawDataToDomain } from '@/api/admin/domains/getDomains';


export default (id: number, include: string[] = []): Promise<Domain> => {
    return new Promise((ok,rej)=>{
        http.get(`/api/application/domains/${id}`,{
            params: {
                include: include.join(",")
            }
        })
        .then(({data})=> ok(rawDataToDomain(data)))
        .catch(rej);
    })
}