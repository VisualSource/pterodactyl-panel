console.log("REPLACE ME WITH REAL PROGRAM");

const CREATE = process.argv.includes("create");
const REMOVE = process.argv.includes("remove");
const ERROR = process.argv.includes("error");

if(CREATE) {
    console.log("CREATEING");
}
if(REMOVE) {
    console.log("REMOVE");
}
if(ERROR) {
    throw new Error("ERRORING OUT");
}

