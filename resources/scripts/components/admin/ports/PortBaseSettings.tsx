import { faCogs } from '@fortawesome/free-solid-svg-icons';
import { useFormikContext } from 'formik';
import tw from 'twin.macro';

import { portMethods, PortType, portTypes} from '@/api/admin/ports/getPorts';

import AdminBox from '@/components/admin/AdminBox';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';

export default ({ children }: React.PropsWithChildren) => {
    const { isSubmitting } = useFormikContext();

    return (
        <AdminBox icon={faCogs} title="Settings" isLoading={isSubmitting}>
             <div css={tw`grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6`}>
                <Field
                    id={'external_port'}
                    name={'external_port'}
                    label={'External Port'}
                    type={'number'}
                />
                
                <div>
                    <Label htmlFor="type">Network Type</Label>
                    <Select id="type" name="type" defaultValue="both">
                        { Object.entries(portTypes).map(([key,value])=>(
                            <option key={value} value={value}>
                                {key}
                            </option>
                        )) }
                    </Select>
                </div>

                
                <div>
                    <Label htmlFor="method">Protcal Method</Label>
                    <Select id="method" name="method" defaultValue="upnp">
                        { Object.entries(portMethods).map(([key,value])=>(
                            <option key={value} value={value}>
                                {key}
                            </option>
                        )) }
                    </Select>
                </div>

                
                {children}
            </div>
        </AdminBox>
    );
}