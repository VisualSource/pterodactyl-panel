import { faCogs } from '@fortawesome/free-solid-svg-icons';
import { useFormikContext } from 'formik';
import tw from 'twin.macro';

import { portMethods, PortType, portTypes} from '@/api/admin/ports/getPorts';

import AdminBox from '@/components/admin/AdminBox';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';


const AdvPortSettings = () => {
    const { isSubmitting, setFieldValue, initialValues } = useFormikContext<{ internal_address?: any, internal_port?: any, method?: any;  }>();
    return (
        <AdminBox icon={faCogs} title="Adv. Settings" isLoading={isSubmitting}>
            <div css={tw`grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6`}>
                <Field defaultValue={initialValues?.internal_port} id='internal_port' name='internal_port' label='Interal Port' type='number'/>

                <Field defaultValue={initialValues?.internal_address} id='internal_address' name='internal_address' label='Internal Address' type='text'/>

                <div>
                    <Label htmlFor="method">Protcal Method</Label>
                    <Select id="method" name="method" defaultValue={initialValues?.method ?? "upnp"} onChange={(ev)=>setFieldValue("method",ev.target.value)}>
                        { Object.entries(portMethods).map(([key,value])=>(
                            <option key={value} value={value}>
                                {key}
                            </option>
                        )) }
                    </Select>
                </div>
            </div>
        </AdminBox>
    );
}
export default AdvPortSettings;